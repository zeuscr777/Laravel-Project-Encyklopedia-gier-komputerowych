<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Games;
use App\Models\tutorials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class AdminController extends Controller
{
    public function view_category()
    {
        $data=category::all();

        return view('admin.category', compact('data'));
    }

    public function add_category(Request $request)
    {
        $data=new category;

        $data->category_name=$request->category;

        $data->save();

        return redirect()->back()->with('message', 'Kategoria dodana poprawnie!');
    }

    public function delete_category($id)
    {
        $data = category::find($id);

        $data -> delete();

        return redirect()->back()->with('message', 'Kategoria została usunięta!');
    }

    public function view_addGame()
    {
        $category=category::all();
        return view('admin.addGame', compact('category'));
    }

    public function add_game(Request $request)
    {
        $game = new games();

        $game->title=$request->title;

        $game->description=$request->description;

        $game->year=$request->year;

        $game->mark=$request->mark;

        $game->category=$request->category;

        $image=$request->image;

        $imagename=time().'.'.$image->getClientOriginalExtension();

        $request->image->move('game', $imagename);

        $game->image=$imagename;

        $game->save();

        return redirect()->back()->with('message', 'Gra dodana poprawnie!');
    }

    public function view_showGames()
    {
        $games = games::paginate(5);
        return view('admin.showGames', compact('games'));
    }

    public function delete_game($id)
    {
        $game = games::find($id);

        $game -> delete();

        return redirect()->back()->with('message', 'Gra została usunięta!');
    }

    public function update_game($id)
    {
        $game = games::find($id);

        $category = category::all();

        return view('admin.updateGame', compact('game', 'category'));
    }

    public function update_game_confirm(Request $request, $id)
    {
        $game=games::find($id);

        $game->title=$request->title;

        $game->description=$request->description;

        $game->year=$request->year;

        $game->mark=$request->mark;

        $game->category=$request->category;

        $image=$request->image;

        if($image)
        {
            $imagename=time().'.'.$image->getClientOriginalExtension();

            $request->image->move('game', $imagename);

            $game->image=$imagename;
        }

        $game->save();

        return redirect()->back()->with('message', 'Gra zaktualizowana poprawnie!');
    }
    public function view_addTutorial()
    {
        $game=games::all();
        return view('admin.addTutorial', compact('game'));
    }
    public function add_tutorial(Request $request)
    {
        $game = tutorials::firstOrCreate(['title' => $request->title]);
        if ($game->wasRecentlyCreated) {
            $game->content = $request->editor1;

            $game->content = str_replace(PHP_EOL, '<br>', $game->content);
            $game->content = preg_replace('/(<br\s*\/?>\s*){2,}/', '<br>', $game->content);

            $game->save();
            return redirect()->back()->with(['message' => 'Poradnik do gry dodany poprawnie!', 'alert-type' => 'success']);
        } else {
            $game->content = $request->editor1;

            $game->content = str_replace(PHP_EOL, '<br>', $game->content);
            $game->content = preg_replace('/(<br\s*\/?>\s*){2,}/', '<br>', $game->content);

            $game->save();
            return redirect()->back()->with(['message' => 'Poradnik do gry zaktualizowany poprawnie!', 'alert-type' => 'success']);
        }
    }

    public function view_showTutorials()
    {;
        $tutorials = DB::table('tutorials')
            ->join('games', 'tutorials.title', '=', 'games.title')
            ->select('tutorials.*', 'games.image')
            ->paginate(5);
        return view('admin.showTutorials', compact('tutorials'));
    }

    public function delete_tutorial($id)
    {
        $game = tutorials::find($id);

        $game -> delete();

        return redirect()->back()->with('message', 'Gra została usunięta!');
    }

    public function update_tutorial($id)
    {
        $game = tutorials::find($id);

        return view('admin.updateTutorial', compact('game'));
    }

    public function update_tutorial_confirm(Request $request, $id)
    {
        $game=tutorials::find($id);

        $game->content = $request->editor1;

        $game->content = str_replace(PHP_EOL, '<br>', $game->content);
        $game->content = preg_replace('/(<br\s*\/?>\s*){2,}/', '<br>', $game->content);

        $game->save();

        return redirect()->back()->with(['message' => 'Poradnik do gry zaktualizowany poprawnie!', 'alert-type' => 'success']);
    }

    public function generate_pdf($id)
    {
        $tutorial = tutorials::find($id);

        $pdf = PDF::loadView('admin/pdf_view', compact('tutorial'));

        return $pdf->download('tutorial.pdf');
    }
}
