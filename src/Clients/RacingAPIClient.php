<?php

namespace RacingPackage\Clients;

use DateTime;
use DateTimeZone;
use Exception;
use RacingPackage\Contracts\ClientInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

class RacingAPIClient extends Client implements ClientInterface
{
    protected $options;

    public function __construct()
    {
        $this->options = [
            'auth' => [
                "username" => env('RACING_API_USERNAME'),
                "password" => env('RACING_API_PASSWORD')
            ]
        ];
    }

    /**
     * Fetch data from the API.
    */
    public function getTodaysMeetings() 
    {
        $todaysRaceCards = $this->request('get', 'racecards/standard', $this->options);

        $meetings = [];
        $convertedMeetings = [];

        // Group raceCards by course name.
        foreach ($todaysRaceCards['racecards'] as $raceCard) {
            $course = $raceCard['course'];

            if (!isset($meetings[$course])) {
                $meetings[$course] = [];
            }

            $meetings[$course][] = $raceCard;
        }

        // Iterate through converted meetings and push to $convertedMeetings.
        foreach ($meetings as $course => $raceCards) {
            $convertedMeetings[] = $this->getMeetings($raceCards);
        }

        return $convertedMeetings;
    }

    public function getTomorrowsMeetings() 
    {
        try {
            $tomorrowRaceCards = $this->request('get', 'https://api.theracingapi.com/v1/racecards/standard?day=tomorrow', $this->options);

            $meetings = [];
            $convertedMeetings = [];

            // Group raceCards by course name.
            foreach ($tomorrowRaceCards['racecards'] as $raceCard) {
                $course = $raceCard['course'];

                if (!isset($meetings[$course])) {
                    $meetings[$course] = [];
                }

                $meetings[$course][] = $raceCard;
            }

            // Iterate through converted meetings and push to $convertedMeetings.
            foreach ($meetings as $course => $raceCards) {
                $convertedMeetings[] = $this->getMeetings($raceCards);
            }

            return $convertedMeetings;

        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function getMeetings($filteredMeetings) 
    {
        $meetings = [];

        foreach ($filteredMeetings as $meeting) {
            $meetings[] = [
                'uid' => Uuid::uuid4(),
                'number' => 0,
                'abandoned' => $meeting['is_abandoned'],
                'name' => $meeting['course'],
                'country_code' => $meeting['region'],
                'type' => $meeting['type'],
                'going_description' => $meeting['going'],
                'pre_filtered_weather' => $meeting['weather'],
                'surface_type' => $meeting['surface'],
                'race_date' => $this->convertToGBTime($meeting['off_dt']),
                'races' => $this->getRaces($meeting),
                'source' => 'theracingapi',
            ];
        }

        return $meetings;
    }

    public function convertToGBTime($auTime) 
    {
        $auTimeUTC = new DateTime($auTime, new DateTimeZone('Australia/Sydney'));
        $auTimeUTC->setTimezone(new DateTimeZone('UTC'));
    
        $gbTime = clone $auTimeUTC;
        $gbTime->setTimezone(new DateTimeZone('Europe/London'));
    
        return $gbTime;
    }

    private function getHorse($runner): array
    {
        if ($runner) {
            return [
                'uid' => substr($runner['horse_id'], 4),
                'name' => $runner['horse'],
                'form' => $runner['form'],
                'spotlight' => $runner['spotlight'],
                'age' => $runner['age'],
                'rating' => $runner['ofr'] != '-' ? $runner['ofr'] : 0,
                'days_since_last_run' => $this->extractInt($runner['last_run']),
                'date_of_birth' => $runner['dob'],
                'owner' => [
                    'uid' => substr($runner['owner_id'], 4),
                    'name' => $runner['owner'],
                ],
                'breeder' => [
                    'name' => $runner['breeder'],
                ],
                'trainer' => [
                    'trainer_id' => substr($runner['trainer_id'], 4),
                    'uid' => substr($runner['trainer_id'], 4),
                    'name' => $runner['trainer'],
                    'shortname' => $runner['trainer'],
                    'country_code' => $runner['trainer_location'],
                    'rtf' => $runner['trainer_rtf'],
                ],
                'dam' => [
                    'uid' => substr($runner['dam_id'], 4),
                    'name' => $runner['dam'],
                    'country' => $runner['dam_region'],
                ],
                'sire' => [
                    'uid' => substr($runner['sire_id'], 4),
                    'name' => $runner['sire'],
                    'country' => $runner['sire_region'],
                ],
                'damsire' => [
                    'uid' => substr($runner['damsire_id'], 4),
                    'name' => $runner['damsire'],
                    'country' => $runner['damsire_region'],
                ],
                'horse_colour_code' => $runner['colour'],
                'horse_sex_code' => $runner['sex_code'],
            ];
        }

        return [];
    }

    private function extractInt($string) 
    {
        $lastRunPattern = '/[^0-9]/';
        $convertedString = preg_replace($lastRunPattern, '', $string);

        if ($convertedString == '')
            return 0;

        return $convertedString;
    }

    /**
     * Build out the races data for a specific meeting.
    */
    private function getRaces($race) 
    {
        $races = [];
    
        // Map through races in meeting.
        // foreach ($meeting as $race) {
            $runners = $this->getRunners($race['runners'], $race['race_id']);
    
            $races[] = [
                'title' => $race['race_name'] ?? "",
                'number' =>  0,
                'surface' => $race['Surface'] ?? "",
                'code' => 0,
                'class' => $race['race_class'] ?? "",
                'uid' => substr($race['race_id'], 4, strlen($race['race_id'])), // rand(1, 2147483647)
                'race_instance_uid' => UUID::uuid4(),
                'distance' => $this->distToYards($race['distance']),
                'runner_count' => $runners ? count($runners) : 0,
                'furlongs' => $race['distance_f'],
                'fences' => null,
                'starts_at' => $this->convertToGBTime($race['off_dt']),
                'runners' => $runners,
            ];
        // }
    
        return $races;
    }
    
    private function distToYards($distance) 
    {
        preg_match_all('/\d+/', $distance, $matches);

        return (($matches[0][0] ?? 0) * 1760) + (($matches[0][1] ?? 0));
    }

    /**
     * Race meeting/race results for a specific region.
     *
     * @param string $date YYYY-MM-DD date string.
     * @return array All results for every meeting/race on a specific date.
    */
    public function getResultsByRegion(string $region)
    {
        return $this->request('get', 'https://api.theracingapi.com/v1/results/today?region=' . $region, $this->options);
    }

    /**
     * All results for a specific race based on the passed race ID.
     *
     * @param int $id The race ID.
     * @return array The results for a specific race.
    */
    public function getResultsById(int $id)
    {
        return $this->request('get', 'https://api.theracingapi.com/v1/results/rac_' . $id, $this->options);
    }

    /**
     * Build out the races data for a specific meeting.
    */
    private function getRunnerOddsValue($runner) 
    {          
        // If there is no odds value yet then return 0.
         if (isset($runner['odds'][1])) {
            // If the odds value is numeric then return it.
            if (is_numeric($runner['odds'][1])) {
                return $runner['odds'][1]['decimal'];
            }
         }

         // If the odds value is not numeric then return 0.
         return 0;
    }

    /**
     * Build out the races data for a specific meeting.
    */
    private function getRunners($runners, $raceID)
    {
        $result = [];
        
        foreach ($runners as $runner) {
            try {
                $nonRunner = $runner['number'] == 'NR';

                $result[] = [
                    'race_id' => substr($raceID, 4),
                    'start_number' => !$nonRunner ? $runner['number'] : 0,
                    'draw' => $runner['draw'] ?? 0,
                    'form' => $runner['form'],
                    'horse' => $this->getHorse($runner),
                    'jockey' => [
                        'uid' => substr($runner['jockey_id'], 4),
                        'jockey_name' => $runner['jockey'],
                        'short_jockey_name' => $runner['jockey'],
                    ],
                    'forecast_odds_desc' => $runner['odds'][1]['fractional'] ?? null,
                    'forecast_odds_value' => $this->getRunnerOddsValue($runner),
                    'non_runner' => $runner['number'] == 'NR' ? 1 : 0,
                    'weight_carried' => $runner['lbs'],
                    'silk_image_path' => $runner['silk_url'] ?? 'none',
                    'course_wins' => $runner['course_wins'] ?? 0,
                    'distance_wins' => $runner['distance_wins'] ?? 0,
                    'race_uid' => substr($raceID, 4),
                    'irish_reserve' => $runner['irish_reserve'] ?? 0,
                ];
            } catch (Exception $e) {
                error_log("Exception occurred: " . $e->getMessage());
            }
        }
        
        return $result;
    }
}
