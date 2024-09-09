<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Initialize the 'todos' session array if it doesn't exist
        if (!Session::has('todos')) {
            Session::put('todos', [
                'Eat breakfast',
                'Go to the gym',
                'Learn Laravel',
                'Go to office',
                'Return home',
                'Sleep',
                'Repeat',
            ]);
        }
    
        // Initialize the 'completed' session array if it doesn't exist
        if (!Session::has('completed')) {
            Session::put('completed', []);
        }
    
        // Retrieve the session data for todos and completed lists
        $todos = Session::get('todos', []);
        $completed = Session::get('completed', []);
    
        return view('todo.index', [
            'todos' => $todos,
            'completed' => $completed,
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // get csrf token
        // $csrf = $request->session()->token();

        // dd(csrf_token(), $csrf);

        $todos = Session::get('todos') ?? [];
        $todos[] = $request->todo;
        Session::put('todos', $todos);

        return redirect()->route('todo.index')->with('success', 'Todo added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($key)
    {
        $todo = '';
        $todos = Session::get('todos') ?? [];
        if (isset($todos[$key])) {
            $todo = $todos[$key];
        }
        return view('todo.index', [
            'todos' => $todos,
            'todo' => $todo,
            'key' => $key,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $key)
    {
        $todos = Session::get('todos') ?? [];
    
        // Update the todo at the specified key
        if (isset($todos[$key])) {
            $todos[$key] = $request->todo;
            Session::put('todos', $todos);
        }
    
        return redirect()->route('todo.index')->with('success', 'Todo updated successfully');
    }
    






    public function markAsCompleted($key)
    {
        $todos = Session::get('todos', []);
        $completed = Session::get('completed', []);

        if (isset($todos[$key])) {
            // Move the todo to the completed list
            $completed[] = $todos[$key];
            unset($todos[$key]);

            // Update the session with the modified lists
            Session::put('todos', $todos);
            Session::put('completed', $completed);
        }

        return redirect()->route('todo.index')->with('success', 'Todo marked as completed');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($key)
    {
        $todos = Session::get('todos', []);
        if (isset($todos[$key])) {
            unset($todos[$key]);
            Session::put('todos', $todos);
        }

        return redirect()->route('todo.index')->with('success', 'Todo deleted successfully');
    }
    public function complete($key)
    {
        $todos = Session::get('todos', []);
        $completed = Session::get('completed', []);

        // Move the todo to the completed list
        if (isset($todos[$key])) {
            $completed[] = $todos[$key];
            unset($todos[$key]);

            // Update the session
            Session::put('todos', $todos);
            Session::put('completed', $completed);
        }

        return redirect()->route('todo.index')->with('success', 'Todo marked as completed');
    }

    /**
     * Reset the completed todos list.
     */
    public function resetCompleted()
    {
        Session::put('completed', []);  // Clear the completed list
        return redirect()->route('todo.index')->with('success', 'All completed todos have been reset');
    }

}
