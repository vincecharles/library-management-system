{{-- Recursive category tree for sidebar navigation --}}
@foreach($categories as $category)
    @if($category->children->count() > 0)
        <div class="nav-dropdown">
            <a href="#" class="nav-link nav-dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#cat-{{ $category->id }}">
                <i class="fas fa-folder"></i>
                <span>{{ $category->name }}</span>
                <i class="fas fa-chevron-down ms-auto nav-chevron"></i>
            </a>
            <div class="collapse" id="cat-{{ $category->id }}">
                <div class="nav-dropdown-items">
                    @foreach($category->children as $child)
                        @if($child->children->count() > 0)
                            {{-- Recurse for deeper nesting --}}
                            @include('components.nav-category-tree', ['categories' => collect([$child])])
                        @else
                            <a href="{{ route('books.index', ['category' => $child->id]) }}"
                               class="nav-link nav-child-link {{ request('category') == $child->id ? 'active' : '' }}">
                                <i class="fas fa-tag"></i>
                                <span>{{ $child->name }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <a href="{{ route('books.index', ['category' => $category->id]) }}"
           class="nav-link {{ request('category') == $category->id ? 'active' : '' }}">
            <i class="fas fa-tag"></i>
            <span>{{ $category->name }}</span>
        </a>
    @endif
@endforeach
