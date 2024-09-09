<x-guest-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Todos') }}
            <span class="text-xl text-black-500">({{ count($todos) }} Todos)</span>
            <span class="text-xl text-green-500 ml-4">Completed ({{ count($completed) }})</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Display Flash Messages -->
                    @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md">
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Add Todo Section (Left) -->
                    <div class="flex flex-col mb-8">
                        <h2 class="text-xl font-semibold mb-4 bottomLine">Add New Todo</h2>
                        <form action="{{ route('todo.store') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="flex items-center">
                                <input type="text" name="todo"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    placeholder="New Todo">
                                <button type="submit"
                                    class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md">
                                    Add
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Todo List and Completed Section (Below Add Section) -->
                    <div class="flex space-x-4">
                        <!-- Left Column: Todo List -->
                        <div class="flex-1 border-r border-gray-200 pr-4">
                            <h2 class="text-xl font-semibold mb-4 bottomLine">Todo List</h2>
                            <ul class="list-disc pl-5 space-y-2 mb-4">
                                @foreach($todos as $key => $todo)
                                <li class="text-gray-700 flex justify-between items-center">
                                    <span id="todo-text-{{ $key }}">{{ $todo }}</span>
                                    <input type="text" id="todo-input-{{ $key }}" value="{{ $todo }}" class="hidden">
                                    <div class="flex space-x-2">
                                        <!-- Edit Button -->
                                        <a href="javascript:void(0)" id="edit-button-{{ $key }}" onclick="editTodo({{ $key }})"
                                            class="px-2 py-1 bg-blue-500 text-white rounded-md">
                                            Edit
                                        </a>

                                        <!-- Done Button -->
                                        <form action="{{ route('todo.complete', $key) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="px-2 py-1 bg-green-500 text-white rounded-md">
                                                Done
                                            </button>
                                        </form>

                                        <!-- Delete Button -->
                                        <form action="{{ route('todo.destroy', $key) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 bg-red-500 text-white rounded-md">
                                                Delete
                                            </button>
                                        </form>

                                        <!-- Update Button (Initially Hidden) -->
                                        <button type="button" id="update-button-{{ $key }}" 
                                            onclick="updateTodo({{ $key }})"
                                            class="px-2 py-1 bg-blue-500 text-white rounded-md hidden">
                                            Update
                                        </button>

                                        <!-- Cancel Button (Initially Hidden) -->
                                        <button type="button" id="cancel-button-{{ $key }}" 
                                            onclick="cancelEdit({{ $key }})"
                                            class="px-2 py-1 bg-gray-500 text-white rounded-md hidden">
                                            Cancel
                                        </button>
                                    </div>

                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Vertical Line -->
                        <div class="w-px bg-gray-200"></div>

                        <!-- Right Column: Completed Todos and Reset Button -->
                        <div class="flex-1 pl-4">
                            <div class="flex flex-col">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-xl font-semibold bottomLine">Completed Todos</h2>
                                    @if(count($completed) > 0)
                                    <form action="{{ route('todo.completed.reset') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="ml-4 px-4 py-1 bg-red-500 text-white rounded-md">
                                            Reset
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                <ul class="list-disc pl-5 space-y-2 mb-4">
                                    @foreach($completed as $completedTodo)
                                    <li class="text-gray-700">
                                        {{ $completedTodo }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom border to cover both columns -->
                    <div class="border-t border-gray-200 pt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for toggling edit mode and updating -->
    <script>
        function editTodo(key) {
            // Hide Edit, Done, Delete buttons and show Update, Cancel buttons
            document.getElementById('edit-button-' + key).classList.add('hidden');
            document.getElementById('update-button-' + key).classList.remove('hidden');
            document.getElementById('cancel-button-' + key).classList.remove('hidden');

            // Show input field for editing
            document.getElementById('todo-text-' + key).style.display = 'none';
            document.getElementById('todo-input-' + key).classList.remove('hidden');
        }

        function cancelEdit(key) {
            // Show Edit, Done, Delete buttons and hide Update, Cancel buttons
            document.getElementById('edit-button-' + key).classList.remove('hidden');
            document.getElementById('update-button-' + key).classList.add('hidden');
            document.getElementById('cancel-button-' + key).classList.add('hidden');

            // Hide input field and show text
            document.getElementById('todo-text-' + key).style.display = 'inline';
            document.getElementById('todo-input-' + key).classList.add('hidden');
        }

        function updateTodo(key) {
            // Get updated value from input
            const updatedTodo = document.getElementById('todo-input-' + key).value;

            // Create a form dynamically to submit the update
            const form = document.createElement('form');
            form.action = "{{ url('todo') }}/" + key;
            form.method = 'POST';

            // Add CSRF token and method field
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';

            // Add the updated todo input to the form
            const todoInput = document.createElement('input');
            todoInput.type = 'hidden';
            todoInput.name = 'todo';
            todoInput.value = updatedTodo;

            // Append inputs to the form and submit
            form.appendChild(tokenInput);
            form.appendChild(methodInput);
            form.appendChild(todoInput);
            document.body.appendChild(form);
            form.submit();

            // After updating, show Edit, Done, Delete buttons and hide Update, Cancel buttons
            document.getElementById('edit-button-' + key).classList.remove('hidden');
            document.getElementById('update-button-' + key).classList.add('hidden');
            document.getElementById('cancel-button-' + key).classList.add('hidden');
            document.getElementById('todo-text-' + key).textContent = updatedTodo;
            document.getElementById('todo-text-' + key).style.display = 'inline';
            document.getElementById('todo-input-' + key).classList.add('hidden');
        }
    </script>
    <style>
        .w-px {
            width: 1px;
        }

        .bg-gray-200 {
            background-color: #e5e7eb;
        }

        .border-t {
            border-top: 2px solid #e5e7eb;
        }

        .bottomLine {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
        }
    </style>

</x-guest-layout>