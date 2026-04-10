<?php
namespace Database\Seeders;
use App\Models\Office; use App\Models\Report; use App\Models\ReportTimeline; use App\Models\User;
use Illuminate\Database\Seeder; use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $offices = [
            ['slug'=>'electricity','name'=>'Electricity Office','icon'=>'⚡','description'=>'Power supply, streetlights and electrical infrastructure.'],
            ['slug'=>'water','name'=>'Water & Sewage Office','icon'=>'💧','description'=>'Water supply, pipe repairs and sewage systems.'],
            ['slug'=>'roads','name'=>'Roads & Transport Office','icon'=>'🛣️','description'=>'Road maintenance, potholes and traffic infrastructure.'],
            ['slug'=>'waste','name'=>'Waste Management Office','icon'=>'🗑️','description'=>'Garbage collection, recycling and waste disposal.'],
            ['slug'=>'parks','name'=>'Parks & Environment Office','icon'=>'🌳','description'=>'Public parks, green spaces and environmental concerns.'],
            ['slug'=>'health','name'=>'Public Health Office','icon'=>'🏥','description'=>'Public health hazards, clinics and sanitation.'],
            ['slug'=>'education','name'=>'Education Office','icon'=>'🏫','description'=>'School infrastructure, resources and education services.'],
            ['slug'=>'safety','name'=>'Public Safety Office','icon'=>'🔒','description'=>'Law enforcement and emergency response.'],
            ['slug'=>'telecom','name'=>'Telecommunications Office','icon'=>'📡','description'=>'Internet, phone and telecommunications infrastructure.'],
            ['slug'=>'infrastructure','name'=>'Infrastructure Office','icon'=>'🏗️','description'=>'General construction and public infrastructure.'],
        ];
        foreach ($offices as $o) Office::create(array_merge($o,['is_active'=>true]));

        $citizen = User::create(['name'=>'Demo User','email'=>'user@demo.com','password'=>Hash::make('password'),'role'=>'user','is_active'=>true]);
        User::create(['name'=>'John Staff','email'=>'staff@demo.com','password'=>Hash::make('password'),'role'=>'office_staff','office_id'=>'electricity','is_active'=>true]);
        User::create(['name'=>'Mary Roads','email'=>'roads@demo.com','password'=>Hash::make('password'),'role'=>'office_staff','office_id'=>'roads','is_active'=>true]);
        User::create(['name'=>'Super Admin','email'=>'admin@demo.com','password'=>Hash::make('password'),'role'=>'super_admin','is_active'=>true]);

        $noteMap = ['Submitted'=>'Report received by CivicPulse','In Progress'=>'Office staff started working on this','Resolved'=>'Issue resolved by the office'];
        $demo = [
            ['category'=>'electricity','office_id'=>'electricity','office_name'=>'Electricity Office','location'=>'Ilala, Uhuru Street','description'=>'Streetlights have been out for 3 weeks on Uhuru Street. Very dark and unsafe at night for residents.','priority'=>'High','status'=>'In Progress','submitter'=>'Alice K.','user_id'=>null,'timeline'=>['Submitted','Assigned','In Progress']],
            ['category'=>'water','office_id'=>'water','office_name'=>'Water & Sewage Office','location'=>'Kinondoni, Mwananyamala Road','description'=>'Main water pipe burst near the junction. Water flooding the road for 2 days and residents cannot access clean water.','priority'=>'High','status'=>'Assigned','submitter'=>'John M.','user_id'=>null,'timeline'=>['Submitted','Assigned']],
            ['category'=>'roads','office_id'=>'roads','office_name'=>'Roads & Transport Office','location'=>"Temeke, Chang'ombe Road",'description'=>'Large potholes on the main road causing accidents. Several vehicles damaged this week and road becoming impassable.','priority'=>'High','status'=>'Resolved','submitter'=>'Sarah L.','user_id'=>null,'feedback'=>'Very quick response! Road fixed in 5 days.','rating'=>5,'timeline'=>['Submitted','Assigned','In Progress','Resolved']],
            ['category'=>'roads','office_id'=>'roads','office_name'=>'Roads & Transport Office','location'=>'Ubungo, Morogoro Road','description'=>'Traffic lights at the Ubungo interchange not working for a week, causing major congestion during peak hours.','priority'=>'High','status'=>'Resolved','submitter'=>$citizen->name,'user_id'=>$citizen->id,'feedback'=>'Fixed in 3 days. Impressed!','rating'=>4,'timeline'=>['Submitted','Assigned','In Progress','Resolved']],
            ['category'=>'electricity','office_id'=>'electricity','office_name'=>'Electricity Office','location'=>'Mikocheni, Chole Road','description'=>'Power outage ongoing for 18 hours with no communication from authority about restoration timeline.','priority'=>'High','status'=>'In Progress','submitter'=>$citizen->name,'user_id'=>$citizen->id,'timeline'=>['Submitted','Assigned','In Progress']],
            ['category'=>'waste','office_id'=>'waste','office_name'=>'Waste Management Office','location'=>'Kariakoo Market Area','description'=>'Garbage not collected for 10 days. Huge pile-up creating serious health hazard near market entrance.','priority'=>'Medium','status'=>'In Progress','submitter'=>'Peter A.','user_id'=>null,'timeline'=>['Submitted','Assigned','In Progress']],
        ];
        foreach ($demo as $d) {
            $r = Report::create(['report_number'=>Report::generateNumber(),'user_id'=>$d['user_id']??null,'submitter_name'=>$d['submitter'],'submitter_email'=>null,'category'=>$d['category'],'office_id'=>$d['office_id'],'office_name'=>$d['office_name'],'location'=>$d['location'],'description'=>$d['description'],'priority'=>$d['priority'],'status'=>$d['status'],'feedback'=>$d['feedback']??null,'feedback_rating'=>$d['rating']??null,'created_at'=>now()->subDays(rand(1,14)),'updated_at'=>now()->subDays(rand(0,3))]);
            foreach ($d['timeline'] as $status) {
                $note = $status==='Assigned' ? "Automatically routed to {$d['office_name']}" : $noteMap[$status];
                ReportTimeline::create(['report_id'=>$r->id,'status'=>$status,'note'=>$note,'created_at'=>now()->subDays(rand(0,10)),'updated_at'=>now()]);
            }
        }
    }
}
