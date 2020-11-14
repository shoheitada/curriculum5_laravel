<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Todo; //追記

class todoController extends Controller
{
  public function add()
  {
      return view('todo.create');
  }

  // 以下を追記
  public function create(Request $request)
  {
    // dd($request);
  // Varidationを行う
  $this->validate($request, Todo::$rules);
  $todo = new Todo;
  $form = $request->all();

  // フォームから送信されてきた_tokenを削除する
  unset($form['_token']);
  $todo->fill($form);
  $todo->is_complete = 0;
  
  // データベースに保存する
  $todo->save();
  return redirect('admin/todo/'); 
  }

  public function index(Request $request) {
    $cond_title = $request->cond_title;
    if ($cond_title != '') {
        // 検索されたら検索結果を取得する
        $todos = Todo::where('title', $cond_title)->get();
    } else {
        // それ以外はすべて取得する
        $todos = Todo::where('is_complete', 0)->get();
    }
    return view('todo.index', ['todos' => $todos, 'cond_title' => $cond_title]);
    }

    public function edit(Request $request)
    {
        // Todo Modelからデータを取得する
        $todos = Todo::find($request->id);
        if (empty($todos)) {
          abort(404);    
        }
        return view('todo.edit', ['todo_form' => $todos]);
    }
  
    public function update(Request $request)
    {
      // Validationをかける
      $this->validate($request, Todo::$rules);
      // Todo Modelからデータを取得する
      $todo = Todo::find($request->get('id'));
      // 送信されてきたフォームデータを格納する
      $todo_form = $request->all();
  
      unset($todo_form['_token']);
      unset($todo_form['remove']);
  
      // 該当するデータを上書きして保存する
      $todo->fill($todo_form)->save();
  
      return redirect('todo');
    }
    public function complete(Request $request)
  {
    // 該当するTodo Modelを取得

    $todo = Todo::find($request->id);
    $todo->is_complete = 1;
    $todo->save();
    //dd($todo->is_complete);

    return redirect('todo/complete_list');
  }

  public function complete_list()
{
  // 完了フラグがtrueのデータのみ取得
  $posts = Todo::where('is_complete',1)->get();

  return view('todo.complete_list', ['posts' => $posts]);
}

public function incomplete(Request $request)
  {
    // 該当するTodo Modelを取得

    $todo = Todo::find($request->id);
    $todo->is_complete = 0;
    $todo->save();

    return redirect('todo/');
  }
  public function delete(Request $request)
  {
      // 該当するTodo Modelを取得
      $todos = Todo::find($request->id);
      // 削除する
      $todos->delete();
      return redirect('todo/');
  }
}