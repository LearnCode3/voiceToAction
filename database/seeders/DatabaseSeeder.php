<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\Report;
use App\Models\ReportTimeline;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------
        // 1. Seed offices
        // ----------------------------------------------------------
        $officeData = [
            ['slug'=>'electricity',    'name'=>'Electricity Office',        'icon'=>'⚡','description'=>'Handles power supply, streetlights and electrical infrastructure.'],
            ['slug'=>'water',          'name'=>'Water & Sewage Office',      'icon'=>'💧','description'=>'Manages water supply, pipe repairs and sewage systems.'],
            ['slug'=>'roads',          'name'=>'Roads & Transport Office',   'icon'=>'🛣️','description'=>'Responsible for road maintenance, potholes and traffic.'],
            ['slug'=>'waste',          'name'=>'Waste Management Office',    'icon'=>'🗑️','description'=>'Oversees garbage collection, recycling and waste disposal.'],
            ['slug'=>'parks',          'name'=>'Parks & Environment Office', 'icon'=>'🌳','description'=>'Maintains public parks, green spaces and environmental concerns.'],
            ['slug'=>'health',         'name'=>'Public Health Office',       'icon'=>'🏥','description'=>'Addresses public health hazards, clinics and sanitation.'],
            ['slug'=>'education',      'name'=>'Education Office',           'icon'=>'🏫','description'=>'Handles school infrastructure, resources and education services.'],
            ['slug'=>'safety',         'name'=>'Public Safety Office',       'icon'=>'🔒','description'=>'Manages law enforcement and emergency response issues.'],
            ['slug'=>'telecom',        'name'=>'Telecommunications Office',  'icon'=>'📡','description'=>'Deals with internet, phone and telecommunications infrastructure.'],
            ['slug'=>'infrastructure', 'name'=>'Infrastructure Office',      'icon'=>'🏗️','description'=>'Handles general construction and public infrastructure.'],
        ];
        foreach ($officeData as $o) {
            Office::create(array_merge($o, ['is_active' => true]));
        }

        // ----------------------------------------------------------
        // 2. Seed users
        // ----------------------------------------------------------
        $citizen = User::create([
            'name'      => 'Demo User',
            'email'     => 'user@demo.com',
            'password'  => Hash::make('password'),
            'role'      => 'user',
            'is_active' => true,
        ]);

        // Staff member — electricity office
        User::create([
            'name'      => 'John Staff',
            'email'     => 'staff@demo.com',
            'password'  => Hash::make('password'),
            'role'      => 'office_staff',
            'office_id' => 'electricity',
            'is_active' => true,
        ]);

        // Second staff — roads office (shows multiple staff feature)
        User::create([
            'name'      => 'Mary Roads',
            'email'     => 'roads@demo.com',
            'password'  => Hash::make('password'),
            'role'      => 'office_staff',
            'office_id' => 'roads',
            'is_active' => true,
        ]);

        // Super admin
        User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@demo.com',
            'password'  => Hash::make('password'),
            'role'      => 'super_admin',
            'is_active' => true,
        ]);

        // ----------------------------------------------------------
        // 3. Seed reports
        // ----------------------------------------------------------
        $reports = [
            ['category'=>'electricity','office_id'=>'electricity','office_name'=>'Electricity Office','location'=>'Ilala, Uhuru Street','description'=>'Streetlights have been out for 3 weeks on Uhuru Street. The area is very dark at night and feels unsafe for residents.','priority'=>'High','status'=>'In Progress','submitter'=>'Alice K.','user_id'=>null,'timeline'=>['Submitted','Assigned','In Progress']],
            ['category'=>'water','office_id'=>'water','office_name'=>'Water & Sewage Office','location'=>'Kinondoni, Mwananyamala Road','description'=>'Main water pipe burst near the junction. Water has been flooding the road for 2 days and residents cannot access clean water.','priority'=>'High','status'=>'Assigned','submitter'=>'John M.','user_id'=>null,'timeline'=>['Submitted','Assigned']],
            ['category'=>'roads','office_id'=>'roads','office_name'=>'Roads & Transport Office','location'=>"Temeke, Chang'ombe Road",'description'=>'Large potholes on the main road causing accidents. Several vehicles have been damaged this week and the road is becoming impassable.','priority'=>'High','status'=>'Resolved','submitter'=>'Sarah L.','user_id'=>null,'feedback'=>'Very quick response! Road was repaired within 5 days.','rating'=>5,'timeline'=>['Submitted','Assigned','In Progress','Resolved']],
            ['category'=>'waste','office_id'=>'waste','office_name'=>'Waste Management Office','location'=>'Kariakoo Market Area','description'=>'Garbage has not been collected for 10 days. Huge pile-up creating a serious health hazard near the market entrance.','priority'=>'Medium','status'=>'In Progress','submitter'=>'Peter A.','user_id'=>null,'timeline'=>['Submitted','Assigned','In Progress']],
            ['category'=>'roads','office_id'=>'roads','office_name'=>'Roads & Transport Office','location'=>'Ubungo, Morogoro Road','description'=>'Traffic lights at the Ubungo interchange have not been working for a week, causing major congestion during peak hours.','priority'=>'High','status'=>'Resolved','submitter'=>$citizen->name,'user_id'=>$citizen->id,'feedback'=>'Lights were fixed within 3 days. Impressed with the fast response!','rating'=>4,'timeline'=>['Submitted','Assigned','In Progress','Resolved']],
            ['category'=>'electricity','office_id'=>'electricity','office_name'=>'Electricity Office','location'=>'Mikocheni, Chole Road','description'=>'Power outage has been ongoing for 18 hours with no communication from the electricity authority about restoration timeline.','priority'=>'High','status'=>'In Progress','submitter'=>$citizen->name,'user_id'=>$citizen->id,'timeline'=>['Submitted','Assigned','In Progress']],
            ['category'=>'health','office_id'=>'health','office_name'=>'Public Health Office','location'=>'Magomeni Dispensary','description'=>'Long queues and shortage of basic medicines at the local dispensary. Patients are being turned away daily due to stock-outs.','priority'=>'High','status'=>'Assigned','submitter'=>'Grace N.','user_id'=>null,'timeline'=>['Submitted','Assigned']],
            ['category'=>'parks','office_id'=>'parks','office_name'=>'Parks & Environment Office','location'=>'Mnazi Mmoja Park','description'=>'Broken playground equipment is dangerous for children. The swing set has rusted and collapsed posing serious injury risk.','priority'=>'Medium','status'=>'Assigned','submitter'=>'Mary T.','user_id'=>null,'timeline'=>['Submitted','Assigned']],
        ];

        $noteMap = [
            'Submitted'   => 'Report received by VoiceToAction',
            'In Progress' => 'Office staff has started working on this issue',
            'Resolved'    => 'Issue has been successfully resolved',
        ];

        foreach ($reports as $data) {
            $report = Report::create([
                'report_number'   => Report::generateNumber(),
                'user_id'         => $data['user_id'] ?? null,
                'submitter_name'  => $data['submitter'],
                'submitter_email' => null,
                'category'        => $data['category'],
                'office_id'       => $data['office_id'],
                'office_name'     => $data['office_name'],
                'location'        => $data['location'],
                'description'     => $data['description'],
                'priority'        => $data['priority'],
                'status'          => $data['status'],
                'feedback'        => $data['feedback'] ?? null,
                'feedback_rating' => $data['rating'] ?? null,
                'created_at'      => now()->subDays(rand(1,14)),
                'updated_at'      => now()->subDays(rand(0,3)),
            ]);

            foreach ($data['timeline'] as $status) {
                $note = $status === 'Assigned'
                    ? "Automatically routed to {$data['office_name']}"
                    : $noteMap[$status];
                ReportTimeline::create([
                    'report_id'  => $report->id,
                    'status'     => $status,
                    'note'       => $note,
                    'created_at' => now()->subDays(rand(0,10)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
