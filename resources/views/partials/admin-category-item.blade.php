<arcticle class="category-item">
    {{ $item }}
    <section class="admin-category-buttons">
        @csrf
        <button onclick="deleteCategory('{{ $item }}')" class="btn btn-danger">Delete</button>
    </section>
</arcticle>