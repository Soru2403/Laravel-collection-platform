<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumComment;

class ForumController extends Controller
{

    // Foruma galvenā lapa — visi ieraksti
    public function index(Request $request)
    {
        $validSortDirections = ['asc', 'desc']; // Pieļaujamās kārtošanas kārtības
        $sort = $request->get('sort', 'desc');
    
        // Pārbaudām, vai 'sort' ir pareiza vērtība
        if (!in_array($sort, $validSortDirections)) {
            $sort = 'desc'; // Noklusējuma vērtība
        }
    
        // Ierakstu iegūšana ar ierobežotu daudzumu vienā lapā
        $posts = ForumPost::with('comments')
                          ->orderBy('created_at', $sort)
                          ->paginate(5); // Katras lapas ierakstu skaits
    
        return view('pages.forum.index', compact('posts', 'sort'));
    }
    
    // Jauna ieraksta izveide
    public function create()
    {
        return view('pages.forum.create');
    }

    // Jauna ieraksta saglabāšana
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        auth()->user()->forumPosts()->create($request->only(['title', 'keywords', 'content']));

        return redirect()->route('forum.index')->with('success', 'Ieraksts veiksmīgi izveidots!');
    }

    // Viena ieraksta lapa ar komentāriem
    public function show($id)
    {
        $post = ForumPost::with('comments.user')->findOrFail($id);
        $editCommentId = session('edit_comment_id'); // Iegūstam rediģējamā komentāra ID no sesijas
        return view('pages.forum.show', compact('post', 'editCommentId')); // Nododam datus uz skatu
    }

    // Komentāra pievienošana ierakstam
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $post = ForumPost::findOrFail($id);
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Komentārs pievienots!');
    }

    // Metode postu rediģēšanai
    public function edit($id)
    {
        // Atrodam ierakstu pēc ID
        $post = ForumPost::findOrFail($id);

        // Pārliecināmies, ka lietotājs ir šo ierakstu radījis
        if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
            // Ja lietotājs nav šī ieraksta autors vai administrators, atgriežam kļūdu
            return redirect()->route('forum.index')->with('error', 'Jums nav tiesību rediģēt šo ierakstu.');
        }

        // Pārsūtām ierakstu uz rediģēšanas skatu
        return view('pages.forum.edit', compact('post'));
    }

    // Atjaunina ieraksta saturu
    public function update(Request $request, $id)
    {
        $post = ForumPost::findOrFail($id);

        // Pārbaudām, vai lietotājs ir šī ieraksta autors vai administrators
        if (auth()->user()->id !== $post->user_id && !auth()->user()->isAdmin()) {
            return redirect()->route('forum.index')->with('error', 'Jums nav tiesību atjaunināt šo ierakstu.');
        }

        // Validējam iesniegto formu
        $request->validate([
            'title' => 'required|string|max:255',
            'keywords' => 'nullable|string|max:255',
            'content' => 'required|string',
        ]);

        // Atjauninām ieraksta datus
        $post->update($request->only(['title', 'keywords', 'content']));

        // Pārsūtām lietotāju uz foruma sākumlapu ar veiksmīgas atjaunināšanas ziņojumu
        return redirect()->route('forum.show', $post->id)->with('success', 'Ieraksts veiksmīgi atjaunināts!');
    }

    // Aktivizē rediģēšanas režīmu priekš komentāra
    public function editComment(Request $request, $postId, $commentId)
    {
        $comment = ForumComment::findOrFail($commentId);

        // Pārbaudām, vai lietotājs ir šī komentāra autors
        if (auth()->check() && auth()->user()->id === $comment->user_id) {
            // Saglabājam rediģējamā komentāra ID sesijā
            $request->session()->put('edit_comment_id', $comment->id);
        }

        // Pāradresējam atpakaļ uz ieraksta skatu
        return redirect()->route('forum.show', $postId);
    }

    // Atjaunina komentāra saturu
    public function updateComment(Request $request, $postId, $commentId)
    {
        $comment = ForumComment::findOrFail($commentId);

        // Pārbaudām, vai lietotājs ir šī komentāra autors
        if (auth()->check() && auth()->user()->id === $comment->user_id) {
            $request->validate([
                'content' => 'required|string',
            ]);

            $comment->update($request->only('content'));

            // Noņemam rediģēšanas režīmu
            $request->session()->forget('edit_comment_id');
        } else {
            return back()->with('error', 'Jums nav tiesību rediģēt šo komentāru.');
        }

        // Atgriežam lietotāju uz ieraksta lapu
        return redirect()->route('forum.show', $postId)->with('success', 'Komentārs veiksmīgi atjaunināts!');
    }

    // Atceļ rediģēšanas režīmu
    public function cancelEditComment(Request $request, $postId)
    {
        // Notīram rediģēšanas režīmu no sesijas
        $request->session()->forget('edit_comment_id');

        return redirect()->route('forum.show', $postId);
    }

    // Ieraksta dzēšana
    public function destroyPost($id)
    {
        $post = ForumPost::findOrFail($id);

        // Pārbaudām, vai lietotājs ir administrators
        if (auth()->user()->isAdmin()) {
            $post->delete();
            return redirect()->route('forum.index')->with('success', 'Ieraksts dzēsts.');
        }

        // Pārbaudām, vai lietotājs ir šī ieraksta autors
        if (auth()->user()->id !== $post->user_id) {
            return redirect()->route('forum.index')->with('error', 'Jums nav tiesību dzēst šo ierakstu.');
        }

        $post->delete();
        return redirect()->route('forum.index')->with('success', 'Ieraksts dzēsts.');
    }

    // Komentāra dzēšana
    public function destroyComment($postId, $commentId)
    {
        $comment = ForumComment::findOrFail($commentId);

        // Pārbaudām, vai lietotājs ir administrators
        if (auth()->user()->isAdmin()) {
            $comment->delete();
            return back()->with('success', 'Komentārs dzēsts.');
        }

        // Pārbaudām, vai lietotājs ir šī komentāra autors
        if (auth()->user()->id !== $comment->user_id) {
            return back()->with('error', 'Jums nav tiesību dzēst šo komentāru.');
        }

        $comment->delete();
        return back()->with('success', 'Komentārs dzēsts.');
    }

    // Ierakstu meklēšana pēc atslēgvārdiem un ieraksta nosaukumiem
    public function search(Request $request)
    {
        // Ja nav ievadīts meklēšanas vaicājums, tad atgriežam atpakaļ uz sākuma lapu
        if (!$request->has('query') || empty($request->query('query'))) {
            return redirect()->route('forum.index'); // Pāradresē uz sākuma lapu, ja nav meklēšanas
        }

        // Pārbaudām, vai vaicājums ir derīgs
        $request->validate([
            'query' => 'required|string|min:3', // Pārbaudām, vai vaicājums ir derīgs
        ]);

        $query = $request->input('query');
        $sort = $request->input('sort', 'desc'); // Saņemam kārtošanas vērtību ar noklusējumu 'desc'

        // Meklējam ierakstus pēc nosaukuma vai atslēgvārdiem
        $posts = ForumPost::where('title', 'LIKE', "%{$query}%")
                        ->orWhere('keywords', 'LIKE', "%{$query}%")
                        ->orderBy('created_at', $sort)
                        ->paginate(5); // Katras lapas ierakstu skaits

        return view('pages.forum.index', compact('posts', 'query', 'sort'));
    }
}