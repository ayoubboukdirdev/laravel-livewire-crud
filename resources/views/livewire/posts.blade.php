@push('title')
    Posts
@endpush
<div>
    <!-- table -->
    <div class="container mt-5">
        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h3><strong>Laravel LivewireCRUD</strong></h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 style="float: left;"><strong>All Posts</strong></h5>
                        <button 
                            class="btn btn-sm btn-primary" 
                            style="float: right;" 
                            wire:click="createPost">Add New Post</button>
                    </div>
                    <div class="card-body">
                        @if (session()->has('message'))
                            <div class="alert alert-success text-center">{{ session('message') }}</div>
                        @endif

                        @if (session()->has('exception'))
                            <div class="alert alert-danger text-center">{{ session('exception') }}</div>
                        @endif

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($posts->count() > 0)
                                    @foreach ($posts as $post)
                                        <tr>
                                            <td>{{ $post->id }}</td>
                                            <td>{{ $post->title }}</td>
                                            <td>{{ $post->user->name }}</td>
                                            <td style="text-align: center;">
                                                <button class="btn btn-sm btn-secondary" wire:click="viewPostsDetails({{ $post->id }})">View</button>
                                                <button class="btn btn-sm btn-primary" wire:click="editPosts({{ $post->id }})">Edit</button>
                                                <button class="btn btn-sm btn-danger" wire:click="deleteConfirmation({{ $post->id }})">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center;"><small>No Student Found</small></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="mt-3">{!! $posts->links() !!}</div>
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="addPostModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form wire:submit.prevent="storePost" enctype="multipart/form-data">
                        @csrf

                        {{-- Title --}}
                        <div class="form-group row mb-3">
                            <label for="post_title" class="col-3">Title</label>
                            <div class="col-9">
                                <input type="text" id="post_title" class="form-control" wire:model="post_title">
                                @error('post_title')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="form-group row mb-3">
                            <label for="post_body" class="col-3">Body</label>
                            <div class="col-9">
                                <textarea wire:model="post_body"class="form-control"  name="post_body" id="post_body" cols="30" rows="10"></textarea>
                                @error('post_body')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>  
                        
                        {{-- Image --}}
                        <div class="form-group row mb-3">
                            <label for="post_image" class="col-3">Image</label>
                            <div class="col-9" wire:ignore>
                                <input type="file" wire:model="post_image" name="post_image" id="post_image" class="form-control" onchange="previewImage(event , 'add');">
                                @error('post_image')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                                
                                <img 
                                    src="{{ asset('storage/posts/default_image.png') }}" 
                                    class="mt-3" 
                                    alt="post_image" 
                                    width="200px" height="150px"
                                    id="preview_image"
                                >
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label for="" class="col-3"></label>
                            <div class="col-9">
                                <button type="submit" class="btn btn-sm btn-primary btn-block">Add Post</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="editPostModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form wire:submit.prevent="updatePost">
                        @csrf
                        {{-- Title --}}
                        <div class="form-group row mb-3">
                            <label for="post_title" class="col-3">Title</label>
                            <div class="col-9">
                                <input type="text" id="post_title" class="form-control" wire:model="post_title">
                                @error('post_title')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="form-group row mb-3">
                            <label for="post_body" class="col-3">Body</label>
                            <div class="col-9">
                                <textarea wire:model="post_body"class="form-control"  name="post_body" id="post_body" cols="30" rows="10"></textarea>
                                @error('post_body')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="form-group row mb-3">
                            <label for="post_image" class="col-3">Image </label>
                            <div class="col-9" wire:ignore>
                                <input type="file" wire:model="post_image" name="post_image" id="post_image" class="form-control" onchange="previewImage(event , 'edit' , );">
                                @error('post_image')
                                    <span class="text-danger" style="font-size: 11.5px;">{{ $message }}</span>
                                @enderror
                                
                                <img 
                                    src="{{ asset('storage/posts/default_image.png') }}" 
                                    class="mt-3" 
                                    alt="post_image" 
                                    width="200px" height="150px"
                                    id="preview_image_edit"
                                >
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label class="col-3"></label>
                            <div class="col-9">
                                <button type="submit" class="btn btn-sm btn-primary">Edit Post</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deletePostModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-4 pb-4">
                    <h6>Are you sure? You want to delete this post data!</h6>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-primary" wire:click="cancel()" data-dismiss="modal" aria-label="Close">Cancel</button>
                    <button class="btn btn-sm btn-danger" wire:click="deletePost()">Yes! Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="viewPostModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>ID: </th>
                                <td>{{ $post_id }}</td>
                            </tr>

                            <tr>
                                <th>Title: </th>
                                <td>{{ $post_title }}</td>
                            </tr>

                            <tr>
                                <th>Body: </th>
                                <td>
                                    {{ $post_body }}
                                </td>
                            </tr>
                            <tr>
                                <th>user: </th>
                                <td>
                                    {{ $post_user }}
                                </td>
                            </tr>
                            <tr>
                                <th>Image: </th>
                                <td>
                                    <img 
                                        src="{{ $post_image }}"
                                        class="mt-3" 
                                        alt="post_image" 
                                        width="400px" height="250px"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- Modal --}}
    <script>
        window.addEventListener('close-modal', event =>{
            $('#addPostModal').modal('hide');
            $('#editPostModal').modal('hide');
            $('#deletePostModal').modal('hide');
        });

        window.addEventListener('show-edit-post-modal', event =>{
            $('#editPostModal').modal('show');
        });

        window.addEventListener('show-delete-confirmation-modal', event =>{
            $('#deletePostModal').modal('show');
        });

        window.addEventListener('show-view-post-modal', event =>{
            $('#viewPostModal').modal('show');
        });

        window.addEventListener('show-create-post-modal', event =>{
            $('#addPostModal').modal('show');
        });
    </script>

    {{-- Imag --}}
    <script>
            const previewImage = (event , type) => {

                if(type == 'add'){
                        /**
                         * Get the selected files.
                        */
                    const imageFiles = event.target.files;
                        /**
                         * Count the number of files selected.
                        */
                    const imageFilesLength = imageFiles.length;
                    /**
                        * If at least one image is selected, then proceed to display the preview.
                    */
                    if (imageFilesLength > 0) {
                        /**
                         * Get the image path.
                         */
                        const imageSrc = URL.createObjectURL(imageFiles[0]);
                        /**
                         * Select the image preview element.
                         */
                        const imagePreviewElement = document.querySelector("#preview_image");
                        /**
                         * Assign the path to the image preview element.
                         */
                        imagePreviewElement.src = imageSrc;
                        /**
                         * Show the element by changing the display value to "block".
                         */
                        imagePreviewElement.style.display = "block";
                    }
                }

                if(type == 'edit'){
                    console.log({{ $post_id }});
                        /**
                         * Get the selected files.
                        */
                    const imageFiles = event.target.files;
                        /**
                         * Count the number of files selected.
                        */
                    const imageFilesLength = imageFiles.length;
                    /**
                        * If at least one image is selected, then proceed to display the preview.
                    */
                    if (imageFilesLength > 0) {
                        /**
                         * Get the image path.
                         */
                        const imageSrc = URL.createObjectURL(imageFiles[0]);
                        /**
                         * Select the image preview element.
                         */
                        const imagePreviewElement = document.querySelector("#preview_image_edit");
                        /**
                         * Assign the path to the image preview element.
                         */
                        imagePreviewElement.src = imageSrc;
                        /**
                         * Show the element by changing the display value to "block".
                         */
                        imagePreviewElement.style.display = "block";
                    }
                }
                
            };
    </script>
@endpush
