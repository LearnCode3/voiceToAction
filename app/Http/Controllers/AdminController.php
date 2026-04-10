<?php
namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller {
    /* ── USERS ────────────────────────────────────────────── */
    public function users(Request $request) {

        $filter = $request->get('filter','all');
        $search = $request->get('search','');

        $query  = User::where('role','user')->latest();

        if ($search) $query->where(fn($q)=>$q->where('name','like',"%$search%")->orWhere('email','like',"%$search%"));
        if ($filter==='active') $query->where('is_active',true);
        if ($filter==='banned') $query->where('is_active',false);
        
        $users      = $query->withCount('reports')->paginate(20)->withQueryString();
        $totalUsers = User::where('role','user')->count();
        $active     = User::where('role','user')->where('is_active',true)->count();
        $banned     = User::where('role','user')->where('is_active',false)->count();
        return view('admin.users.index', compact('users','filter','search','totalUsers','active','banned'));
    }
    public function activateUser(User $user) { $user->update(['is_active'=>true]); return back()->with('success',"\"{$user->name}\" activated."); }
    public function banUser(User $user) {
        if ($user->isSuperAdmin()) return back()->with('error','Cannot ban a super administrator.');
        $user->update(['is_active'=>false]); return back()->with('success',"\"{$user->name}\" banned.");
    }
    public function deleteUser(User $user) {
        if ($user->isSuperAdmin()) return back()->with('error','Cannot delete a super administrator.');
        Report::where('user_id',$user->id)->update(['user_id'=>null]);
        $name=$user->name; $user->delete(); return back()->with('success',"\"{$name}\" deleted.");
    }

    /* ── STAFF ────────────────────────────────────────────── */
    public function staff() {
        $staff   = User::where('role','office_staff')->with('office')->latest()->paginate(50);
        $offices = Office::where('is_active',true)->orderBy('name')->get();
        return view('admin.staff.index', compact('staff','offices'));
    }
    public function createStaff(Request $request) {
        $data = $request->validate(['name'=>['required','string','max:255'],'email'=>['required','email','unique:users'],'password'=>['required','string','min:6'],'office_id'=>['required','string','exists:offices,slug']]);
        User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']),'role'=>'office_staff','office_id'=>$data['office_id'],'is_active'=>true]);
        return redirect()->route('admin.staff.index')->with('success',"Staff \"{$data['name']}\" created.");
    }
    public function reassignStaff(Request $request, User $user) {
        $data = $request->validate(['office_id'=>['required','exists:offices,slug']]);
        $user->update(['office_id'=>$data['office_id']]); return back()->with('success',"\"{$user->name}\" reassigned.");
    }
    public function activateStaff(User $user) { $user->update(['is_active'=>true]); return back()->with('success',"\"{$user->name}\" activated."); }
    public function banStaff(User $user)      { $user->update(['is_active'=>false]); return back()->with('success',"\"{$user->name}\" banned."); }
    public function deleteStaff(User $user)   { $name=$user->name; $user->delete(); return back()->with('success',"\"{$name}\" deleted."); }

    /* ── OFFICES ──────────────────────────────────────────── */
    public function officesIndex() {
        $offices = Office::withCount(['reports','admins as staff_count'])->orderBy('name')->get();
        return view('admin.offices.index', compact('offices'));
    }
    public function officesCreate() { return view('admin.offices.create'); }
    public function officesStore(Request $request) {
        $data = $request->validate(['name'=>['required','string','max:255'],'icon'=>['required','string','max:10'],'description'=>['nullable','string','max:500'],'is_active'=>['sometimes','boolean']]);
        $data['slug'] = Str::slug($data['name']); $data['is_active'] = $request->boolean('is_active',true);
        $base=$data['slug']; $i=1;
        while(Office::where('slug',$data['slug'])->exists()) $data['slug']=$base.'-'.$i++;
        Office::create($data);
        return redirect()->route('admin.offices.index')->with('success',"Office \"{$data['name']}\" created.");
    }
    public function officesEdit(Office $office) {
        $office->loadCount('admins as staff_count');
        $staff = User::where('office_id',$office->slug)->where('role','office_staff')->get();
        return view('admin.offices.edit', compact('office','staff'));
    }
    public function officesUpdate(Request $request, Office $office) {
        $data = $request->validate(['name'=>['required','string','max:255'],'icon'=>['required','string','max:10'],'description'=>['nullable','string','max:500'],'is_active'=>['sometimes','boolean']]);
        $data['is_active']=$request->boolean('is_active',false);
        if ($office->name !== $data['name']) Report::where('office_id',$office->slug)->update(['office_name'=>$data['name']]);
        $office->update($data);
        return redirect()->route('admin.offices.index')->with('success',"Office \"{$office->name}\" updated.");
    }
    public function officesDestroy(Office $office) {
        if ($office->reports()->exists()) return back()->with('error',"Cannot delete \"{$office->name}\" — has reports. Deactivate instead.");
        User::where('office_id',$office->slug)->update(['office_id'=>null]);
        $office->delete();
        return redirect()->route('admin.offices.index')->with('success','Office deleted.');
    }
}
