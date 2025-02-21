<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Para gerar slugs (se não usar Eloquent Sluggable)
use Inertia\Inertia;

//Para validar o unique, ignorando o id

class CategoryController extends Controller
{
    public function tree()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children.children.children') // Eager loading recursivo (até 3 níveis)
            ->orderBy('name')
            ->get();

        return Inertia::render('Categories/Tree', [
            'categories' => $categories,
        ]);
    }
    public function index()
    {
        // $categories = Category::whereNull('parent_id')
        //     ->with('children.children') // Eager load até o nível 3
        //     ->orderBy('name')
        //     ->get();
        $categories = Category::all();
        // whereNull('parent_id')
        //     ->with('children.children.children') // Eager loading recursivo (até 3 níveis)
        //     ->orderBy('name')
        //     ->get();

        return Inertia::render('Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::all(); //Todas as categorias para popular o select
        return Inertia::render('Categories/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'scope'             => 'nullable|string',
            'possible_contents' => 'nullable|string',
            'post_suggestions'  => 'nullable|string',
            'parent_id'         => 'nullable|exists:categories,id', // Valida se o parent_id existe na tabela categories
        ]);

        $category = new Category(); //::create($validated); //Cria a categoria
        $category->slug = $request->parent_id > 0 ? criar_slug(Category::find($request->parent_id)->name) ."-".criar_slug($request->name) : criar_slug($request->name);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->scope = $request->scope;
        $category->possible_contents = $request->possible_contents;
        $category->post_suggestions = $request->post_suggestions;
        $category->save();
        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function show($slug)
    {
        $category = Category::where('slug', $slug)->with(['children.children', 'posts'])->firstOrFail();
        return Inertia::render('Categories/Show', [
            'category' => $category,
        ]);
    }

    public function edit(Category $category) // Route Model Binding (busca pelo ID, *não* pelo slug)
    {
        $categories = Category::all();
        return Inertia::render('Categories/Edit', [
            'category'   => $category,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Category $category)
    {

        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'scope'             => 'nullable|string',
            'possible_contents' => 'nullable|string',
            'post_suggestions'  => 'nullable|string',
            'parent_id'         => 'nullable|exists:categories,id',
        ]);

        $category->update($validated); // Atualiza

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso!');
    }
}
