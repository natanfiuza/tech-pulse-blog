<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Str;

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
   /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carrega categorias com seus pais para exibir o nome do pai
        // Ordena para facilitar a visualização da hierarquia (opcional)
        $categories = Category::with('parent')
                            ->orderBy('parent_id') // Ou ordene como preferir
                            ->orderBy('name')
                            ->get();

        return Inertia::render('Admin/Categories/CategoriesIndex', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Busca todas as categorias para o dropdown de seleção de pai
        // Apenas ID e Nome são necessários
        $categories = Category::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Categories/CategoriesCreate', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'scope'             => 'nullable|string',
            'possible_contents' => 'nullable|string',
            'post_suggestions'  => 'nullable|string',
            'parent_id'         => 'nullable|exists:categories,id', // Garante que o ID do pai exista ou seja nulo
        ]);

        $category = new Category();
        $category->uuid = Str::uuid()->toString();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->scope = $request->scope;
        $category->possible_contents = $request->possible_contents;
        $category->post_suggestions = $request->post_suggestions;
        $category->parent_id = $request->parent_id;
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     */
     public function show(Category $category)
     {
         //
     }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Busca todas as categorias EXCETO a própria categoria sendo editada
        // para evitar que uma categoria seja pai de si mesma.
        $available_parents = Category::where('id', '!=', $category->id)
                                    ->orderBy('name')
                                    ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/CategoriesEdit', [
            'category' => $category, // Passa a categoria atual
            'categories' => $available_parents, // Passa os pais disponíveis
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
         $validatedData = $request->validate([
            'name'              => 'required|string|max:255',
            // 'slug' => ['required','string','max:255', Rule::unique('categories')->ignore($category->id)], // Se o slug não for gerado no update
            'description'       => 'nullable|string',
            'scope'             => 'nullable|string',
            'possible_contents' => 'nullable|string',
            'post_suggestions'  => 'nullable|string',
            // Garante que o ID do pai exista ou seja nulo, e não seja a própria categoria
            'parent_id'         => ['nullable', Rule::exists('categories', 'id'), Rule::notIn([$category->id])],
        ]);

        // O slug pode ou não ser atualizado dependendo da config do Sluggable
        $category->update($validatedData);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // A constraint ON DELETE SET NULL cuidará dos filhos
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Categoria excluída com sucesso!');
        } catch (\Exception $e) {
             // Captura erros (ex: restrições de FK se não configurado corretamente)
             Log::error('Erro ao excluir categoria: '.$e->getMessage());
             return redirect()->route('categories.index')->with('error', 'Não foi possível excluir a categoria.');
        }
    }
}
