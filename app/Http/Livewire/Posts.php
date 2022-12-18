<?php

namespace App\Http\Livewire;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Intervention\Image\Facades\Image;

class Posts extends Component
{   
    use WithFileUploads, WithPagination;
    
    public $post_id;
    public $post_title;
    public $post_body;
    public $post_user;
    public $post_image;


    public function all_posts(){
        return Post::orderByDesc('id')->paginate(10);
    }

    public function resetInputs(){
        $this->post_id = '';
        $this->post_title = '';
        $this->post_body = '';
        $this->post_user = '';
    }

    public function close(){
        $this->resetInputs();
    }

    public function uploadImage($slug){
            /* 
            Upload Image
                ----------------------
            */
            // $file_size = $this->post_image->getSize();
            // $file_type = $this->post_image->getMimeType();

            $filename = $slug.'-'.time().'.'.$this->post_image->getClientOriginalExtension();
            if (!file_exists('storage/posts/'.auth()->user()->username)) {
                mkdir('storage/posts/'.auth()->user()->username, 777, true);
            }

            $path = public_path('storage/posts/' . auth()->user()->username . '/' . $filename);

            Image::make($this->post_image->getRealPath())->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path, 100);

            return $filename;
    }

    public function deleteImage($path){
        if (File::exists($path)) {
            unlink($path);
        }
    }

    public function viewPostsDetails($id){
        $post = Post::where('id', $id)->first();

        $this->post_id = $post->id;
        $this->post_title = $post->title;
        $this->post_body = $post->body;
        $this->post_user = $post->user->name;
        $this->post_image = $post->image;

        $this->dispatchBrowserEvent('show-view-post-modal');
    }
    
    public function editPosts($id)
    {
        $post = Post::where('id', $id)->first();

        $this->post_id = $post->id;
        $this->post_title = $post->title;
        $this->post_body = $post->body;
        $this->post_user = $post->user->name;
        $this->post_image = $post->image;

        $this->dispatchBrowserEvent('show-edit-post-modal');
    }

    public function updatePost(){
        //on form submit validation
        $this->validate([
            'post_title'    => 'required|string|unique:posts,id,'.$this->post_id, //Validation with ignoring own data
            'post_body'     => 'required|string',
            'post_image'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' 
        ]);

        $post = Post::where('id', $this->post_id)->first();

        $post->title = $this->post_title;
        $post->body = $this->post_body;

        //upload
        $filename = $this->uploadImage($post->slug);

        $post->image = 'storage/posts/' . auth()->user()->username . '/' .  $filename;
        $post->save();

        session()->flash('message', 'Post has been updated successfully');

        //For hide modal after edit or add post success
        $this->dispatchBrowserEvent('close-modal');
    }

    public function deleteConfirmation($id){
        $this->post_id = $id; //post id

        $this->dispatchBrowserEvent('show-delete-confirmation-modal');
    }

    public function deletePost(){
        $post = Post::where('id', $this->post_id)->first();

        $this->deleteImage($post->image);

        $post->delete();

        session()->flash('message', 'post has been deleted successfully');

        $this->dispatchBrowserEvent('close-modal');
    }

    public function createPost(){
        $this->resetInputs();
        $this->dispatchBrowserEvent('show-create-post-modal');
    }

    public function storePost(){
        
        try {
            //on form submit validation
            $this->validate([
                'post_title'     => 'required|string|unique:posts,title',
                'post_body'      => 'required|string',
                'post_image'     => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            //Add Post Data
            $post = new Post();
            $post->title = $this->post_title;
            $post->image = 'storage/posts/default_image.png';
            $post->body = $this->post_body;
            $post->user_id = auth()->user()->id;

            $post->save();

            //upload
            $filename = $this->uploadImage($post->slug);

            $post->image = 'storage/posts/' . auth()->user()->username . '/' .  $filename;
            $post->save();


            session()->flash('message', 'New Post has been added successfully');

            $this->resetInputs();

            //For hide modal after add student success
            $this->dispatchBrowserEvent('close-modal');


        } catch (\Exception $exc) {
            session()->flash('exception', $exc->getMessage());
            $this->dispatchBrowserEvent('close-modal');
        }
    }

    public function render()
    {

        return view('livewire.posts', [
            'posts' => $this->all_posts(),
        ])->layout('livewire.layouts.master');;
    }

}
