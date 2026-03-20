@extends('layouts.app')

@section('title', 'Manage Topics')

@section('content')
@include('admin.partials.navbar')

<div class="flex-1 flex flex-col items-center p-6 space-y-12 pb-24">
    <div class="w-full max-w-7xl flex items-center justify-between mb-10">
        <div>
            <h1 class="text-6xl font-black italic tracking-tighter uppercase leading-none"><span class="text-gradient">TOPIC</span><br> ARCHIVE</h1>
            <p class="text-gray-400 font-bold tracking-widest uppercase mt-4">Organize your quiz content by categories.</p>
        </div>
        <a href="{{ route('admin.topics.create') }}" class="btn-primary px-8 py-4 rounded-xl font-black text-lg tracking-tighter uppercase shadow-2xl">Create New Topic</a>
    </div>

    @if($topics->isEmpty())
        <div class="card p-20 rounded-3xl w-full max-w-4xl text-center space-y-8 shadow-2xl">
            <div class="text-7xl">🗄️</div>
            <h2 class="text-4xl font-black uppercase text-gray-500 italic tracking-tighter">No Topics Found</h2>
            <p class="text-gray-400 font-bold max-w-lg mx-auto">Build your knowledge base by creating your first topic. You'll need these to categorize your quiz questions!</p>
            <a href="{{ route('admin.topics.create') }}" class="inline-block text-purple-400 hover:text-pink-400 font-black uppercase tracking-widest transition-all">Get Started &rarr;</a>
        </div>
    @else
        <div class="w-full max-w-7xl card rounded-3xl overflow-hidden shadow-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10 uppercase tracking-widest text-xs font-black text-gray-500">
                        <th class="px-8 py-6">ID</th>
                        <th class="px-8 py-6">Topic Name</th>
                        <th class="px-8 py-6">Description</th>
                        <th class="px-8 py-6 text-center">Questions</th>
                        <th class="px-8 py-6">Created On</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($topics as $topic)
                    <tr class="hover:bg-white/5 transition-all text-sm font-bold group">
                        <td class="px-8 py-6 text-gray-500 font-black">#{{ $topic->id }}</td>
                        <td class="px-8 py-6">
                            <span class="text-lg group-hover:text-gradient">{{ $topic->name }}</span>
                        </td>
                        <td class="px-8 py-6 text-gray-400 max-w-xs truncate">{{ $topic->description ?: 'No description' }}</td>
                        <td class="px-8 py-6 text-center">
                            <span class="bg-purple-600/20 text-purple-400 px-3 py-1 rounded-lg text-xs">{{ $topic->questions_count }} Questions</span>
                        </td>
                        <td class="px-8 py-6 text-gray-500">{{ $topic->created_at->format('M d, Y') }}</td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-4" x-data="{ showDelete: false }">
                                <a href="{{ route('admin.topics.edit', $topic->id) }}" class="text-gray-400 hover:text-purple-400 transition-colors uppercase text-xs tracking-widest">Edit</a>
                                
                                <button @click="showDelete = true" class="text-gray-500 hover:text-red-500 transition-colors uppercase text-xs tracking-widest">Delete</button>

                                <!-- Delete Modal Overlay -->
                                <template x-if="showDelete">
                                    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-6 text-left" x-transition>
                                        <div class="card p-10 rounded-3xl w-full max-w-md shadow-2xl" @click.away="showDelete = false">
                                            <h3 class="text-3xl font-black uppercase text-gradient italic tracking-tighter mb-4">Confirm Deletion</h3>
                                            <p class="text-gray-400 font-bold mb-8">Are you sure you want to delete <span class="text-white">{{ $topic->name }}</span>? This action cannot be undone.</p>
                                            <div class="flex space-x-4 justify-end">
                                                <button @click="showDelete = false" class="px-6 py-3 rounded-xl border border-white/10 hover:bg-white/5 transition-all font-bold tracking-widest uppercase text-xs">Cancel</button>
                                                <form action="{{ route('admin.topics.destroy', $topic->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 transition-all font-black tracking-widest uppercase text-xs">Delete Forever</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
