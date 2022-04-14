<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Todo;
use App\Models\Transfer;
use Illuminate\Support\Facades\Mail;

class TodoController extends Controller
{
    public function index(){
        $id = auth()->id();
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $id AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");
        return view('todo.todolist')->with(['todos' => $todos,'week' => true]);
    }

    public function isOnline($site = "https://www.youtube.com/"){
        if(@fopen($site,"r")){
            return true;
        }else{
            return false;
        }
    }

    public function reporttodo(){
        $id = auth()->id();
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $id AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");
        return view('todo.reports.reporttodo')->with(['todos' => $todos,'week' => true]);
    }

    public function reporttododg(){
        $id = auth()->id();
        $uidd = '';
        $dpt = '';
        $dp = DB::select("SELECT * FROM departments limit 1");
        foreach($dp As $ddp){
            $dpt = $ddp->id;
        }
        $uid = DB::select("SELECT * FROM users WHERE dpt_id = $dpt AND id IN(SELECT user_id FROM role_user WHERE role_id = 3)");
        foreach($uid As $ID){
            $uidd = $ID->id;
        }
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $uidd AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $uidd) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");
        return view('todo.reports.reportsDG')->with(['todos' => $todos,'week' => true]);
    }

    public function getTodosWeek(){
        $id = auth()->id();
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $id AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate' UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id) AND created_at BETWEEN '$weekStartDate' AND '$weekEndDate'");
        return view('todo.todolist')->with(['todos' => $todos,'week' => true]);
    }

    public function getTodosAll(){
        $id = auth()->id();
        // $todos = Todo::where('user_id',$id)->orderBy('complited')->get();
        $todos = DB::select("SELECT * FROM todos WHERE user_id = $id UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
        return view('todo.todolist')->with(['todos' => $todos,'week' => false]);
    }

    public function create(){
        return view('todo.newtodo');
    } 

    public function report(Request $request){
        $id = auth()->id();
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $category = $request->category; 
        $datefrom = $request->datefrom; 
        $dateto = $request->dateto;
        $todos = [];

        if($datefrom != '' && $dateto != ''){
            if($datefrom > $dateto){
                return view('todo.reports.reporttodo')->with(['todos' => $todos]);
            }
        }

        if($datefrom == ''){
            $datefrom = $weekStartDate;
        }else{
            if($datefrom > now()){
                return view('todo.reports.reporttodo')->with(['todos' => $todos]);
            }
        }
        if($dateto == ''){
            $dateto = $weekEndDate;
        }else{
            if($dateto > now()){
                return view('todo.reports.reporttodo')->with(['todos' => $todos]);
            }
        }

        if($category == 1){
            $todos = DB::select("SELECT * FROM todos WHERE created_at BETWEEN '$datefrom' AND '$dateto' AND user_id = $id UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
            return view('todo.reports.reporttodo')->with(['todos' => $todos]);
        }elseif($category == 2){
            $todos = DB::select("SELECT * FROM todos WHERE created_at BETWEEN '$datefrom' AND '$dateto' AND user_id = $id AND complited = 1 UNION SELECT * FROM todos WHERE complited = 1 AND id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
            return view('todo.reports.reporttodo')->with(['todos' => $todos]);
        }elseif($category == 3){
            $todos = DB::select("SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
            return view('todo.reports.reporttodo')->with(['todos' => $todos]);
        }elseif($category == 4){
            $todos = DB::select("SELECT * FROM todos WHERE created_at BETWEEN '$datefrom' AND '$dateto' AND user_id = $id AND complited = 0 UNION SELECT * FROM todos WHERE complited = 1 AND id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
            return view('todo.reports.reporttodo')->with(['todos' => $todos]);
        }
    }

    public function reportdg(Request $request){
        $id = auth()->id();
        $uidd = '';
        $todayweek =date("W");
        $carbon = \Carbon\Carbon::now();  
        $weekStartDate = $carbon->startOfWeek()->format('Y-m-d H:i');
        $weekEndDate = $carbon->endOfWeek()->format('Y-m-d H:i');
        $dpt = $request->dpt; 
        $week = $request->week; 
        $todos = [];
        $week = substr($week,6);
        $d1 = $weekStartDate;
        $d2 = $weekEndDate;

        if($week == ''){
            $uid = DB::select("SELECT * FROM users WHERE dpt_id = $dpt AND id IN(SELECT user_id FROM role_user WHERE role_id = 3)");
            foreach($uid As $ID){
                $uidd = $ID->id;
            }
            $todos = DB::select("SELECT * FROM todos WHERE created_at BETWEEN '$d1' AND '$d2' AND user_id = $uidd UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $uidd)");
            return view('todo.reports.reportsDG')->with(['todos' => $todos]);
        }else if($week > $todayweek){
                return view('todo.reports.reportsDG')->with(['todos' => $todos]);
            }else{
                $uid = DB::select("SELECT * FROM users WHERE dpt_id = $dpt AND id IN(SELECT user_id FROM role_user WHERE role_id = 3)");
                foreach($uid As $ID){
                    $uid = $ID->id;
                    $uname = $ID->name;
                }
                // date()
                $year = date('Y');
                $week_start = new \DateTime();
                $week_start->setISODate($year,$week);
                $d1 = $week_start->format('Y-m-d H:i');
                
                $d2 = $d1;
                $d2 = date( "Y-m-d H:i", strtotime( "$d2 +5 day" ) ); 

                $todos = DB::select("SELECT * FROM todos WHERE created_at BETWEEN '$d1' AND '$d2' AND user_id = $uid UNION SELECT * FROM todos WHERE id IN(SELECT todo_id FROM transfers WHERE user_id = $id)");
                return view('todo.reports.reportsDG')->with(['todos' => $todos]);
        }
    }

    public function upload(Request $request){
        $request->validate([
            'title' => 'required',
            'deadline' => 'required'
        ]);

        if($request->deadline < now()){
            return redirect()->back()->with('success', "Please select correct dates");
        }

        $transfer = $request->transfer;
        $transferUser = $request->transferUser;
        $title = $request->title;
        $progress = $request->progress;
        $process = $request->process;
        $output = $request->output;
        $deadline = $request->deadline;
        $transfered = false;
        $reason = "No reason";

        if($output == ""){
            $output = "No autput at the moment";
        }
        
        if($progress == ''){
            $progress = 'No Progress at the moment';
        }

        if($deadline == ''){
            $deadline = now();
        }

        if($process == ''){
            $process = 'No Process at the moment';
        }

        // DG
        if($transfer != ''){
            $transfered = true;
        }

        // director
        if($transferUser != ''){
            $transfered = true;
        }

        $user_id = auth()->id();
        $now = now();
        $todo = Todo::create(['title' => $title,'progress' => $progress,'process' => $process,'output' => $output,'deadline' => $deadline,'completedtime' => $now,'transfered' => $transfered,'user_id' => $user_id,'reason' => $reason]);
        
        if($transfer != ''){
            Transfer::create(['transferDate' => $now,'user_id' => $user_id,'todo_id' => $todo->id,'dpt_id' => $transfer]);
            $name = '';
            $email = '';
            $user = DB::select("SELECT users.name,users.email FROM users,role_user WHERE users.id = role_user.user_id AND role_user.role_id = 3");
            $userfro = DB::select("SELECT * FROM users WHERE id = $user_id");
            $namefro = "";
            foreach($userfro As $fro){
                $namefro = $fro->name;
            }
            foreach($user As $users){
                $name = $users->name;
                $email = $users->email;

                $data = array('name'=>$name, 'email' => $email,'from' => $namefro);
                if($this->isOnline()){
                    Mail::send(['text'=>'transfer'], $data, function($message)use ($users) {
                        $message->to($users->email, $users->name)->subject('ICT Commision Reporter Assignment note');
                        $message->from('info@ictc.go.tz','ICTC');
                    });
                }else{
                    return redirect()->back()->with('success', "Email not sent, check your internet connection.");            
                }
            }
        }

        if($transferUser != ''){
            $dpt = '';
            $userdpts = DB::select("SELECT departments.id FROM users,departments WHERE users.dpt_id = departments.id AND users.id = $transferUser");
            foreach($userdpts As $userdpt){
                $dpt = $userdpt->id;
            }
            Transfer::create(['transferDate' => $now,'user_id' => $transferUser,'todo_id' => $todo->id,'dpt_id' => $dpt]);
            $name = '';
            $email = '';
            $user = DB::select("SELECT users.name,users.email FROM users,role_user,departments WHERE users.dpt_id = departments.id AND users.id = role_user.user_id AND role_user.role_id = 4 AND departments.id = $dpt");
            $userfro = DB::select("SELECT * FROM users WHERE id = $user_id");
            $namefro = "";
            foreach($userfro As $fro){
                $namefro = $fro->name;
            }
            foreach($user As $users){
                $name = $users->name;
                $email = $users->email;

                $data = array('name'=>$name, 'email' => $email,'from' => $namefro);
                if(!$this->isOnline()){
                    Mail::send(['text'=>'transfer'], $data, function($message)use ($users) {
                        $message->to($users->email, $users->name)->subject('ICT Commision Reporter Assignment note');
                        $message->from('info@ictc.go.tz','ICTC');
                    });
                }else{
                    return redirect()->back()->with('success', "Email not sent, check your internet connection.");            
                }
            }
        }
        
        return redirect()->back()->with('success', "TODO created successfully");
    }

    public function edit($id){
        $todo = Todo::find($id);
        return view('todo.edit')->with(['id' => $id, 'todo' => $todo]);
    }

    public function update(Request $request){
        $request->validate([
            'title' => 'required',
            'progress' => 'required',
            'process' => 'required',
            'deadline' => 'required',
            'output' => 'required'
        ]);
        $now = now();
        $updateTodo = Todo::find($request->id);
        $updateTodo->update(['title' => $request->title,'progress' => $request->progress,'process' => $request->process,'deadline' => $request->deadline, 'output' => $request->output, 'completedtime' => $now]);
        return redirect('/dashboard/todolist')->with('success', "Task Updated successfully!");
    }

    public function complited($id){
        $todo = Todo::find($id);
        if($todo->complited){
            $todo->update(['complited' => false, 'completedtime' => now()]);
            return redirect('/dashboard/todolist')->with('success', "Task marked as Incomplete!");
        }else{
            $todo->update(['complited' => true, 'completedtime' => now()]);
            return redirect('/dashboard/todolist')->with('success', "Task marked as Complete!");
        }
    }

    public function delete($id){
        $todo = Todo::find($id);
        $todo->delete();
        return redirect('/dashboard/todolist')->with('success', "Task deleted Successfully!");
    }

    public function getUsersTasktodirector($id){
        return view('director.directordashboard')->with(['userid' => $id]);
    }

    public function getUsersTasktodg1($id){
        return view('dg.dgdashboard')->with(['userid' => $id]);
    }

    public function getUsersTasktodg2($id){
        return view('dg.dgdashboard')->with(['dptid' => $id]);
    }
}