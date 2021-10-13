<h1 class="text-center mt-5">Super mega epic admin panel</h1>
<div class="divider"></div>

<form onsubmit="return confirm('Do you really want to do this?');" action="/settings" method="post">
    <div class="input-group mb-3">
        <button type="submit" class="btn btn-secondary" type="button" id="button-addon1"><span class="material-icons">clear</span>Delete video</button>
        <input autocomplete="off" type="text" class="form-control" placeholder="video id" name="video-id" aria-describedby="button-addon1" required>
    </div>
</form>

<form onsubmit="return confirm('Do you really want to do this?');" action="/settings" method="post">
    <div class="input-group mb-3">
        <button type="submit" class="btn btn-secondary" type="button" id="button-addon2"><span class="material-icons">clear</span>Delete comment</button>
        <input autocomplete="off" type="text" class="form-control" placeholder="comment id" name="comment-id" aria-describedby="button-addon2" required>
    </div>
</form>

<form onsubmit="return confirm('Do you really want to do this?');" action="/settings" method="post">
    <div class="input-group mb-3">
        <button type="submit" class="btn btn-secondary" type="button" id="button-addon3"><span class="material-icons">gavel</span> Delete user</button>
        <input autocomplete="off" type="text" class="form-control" placeholder="user id" name="delete-user" aria-describedby="button-addon3" required>
    </div>
</form>

<form onsubmit="return confirm('Do you really want to do this?');" action="/settings" method="post" enctype="multipart/form-data"> 
    <div class="input-group mb-3">
        <button type="submit" class="btn btn-secondary" type="button" id="button-addon4">Force avatar change</button>
        <input accept="image/*" id="avatar" autocomplete="off" type="file" class="form-control" name="avatar" aria-describedby="button-addon4" required>
        <input autocomplete="off" type="text" class="form-control" placeholder="user id" name="user-id" aria-describedby="button-addon4" required>
    </div>
</form>