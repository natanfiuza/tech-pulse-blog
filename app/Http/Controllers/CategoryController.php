<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Para gerar slugs (se não usar Eloquent Sluggable)
use Inertia\Inertia;
//Para validar o unique, ignorando o id

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with('children.children') // Eager load até o nível 3
            ->orderBy('name')
            ->get();

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

        $category = Category::create($validated); //Cria a categoria
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
