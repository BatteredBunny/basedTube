<form onsubmit="return confirm('Do you really want to do this?');" action="/watch?v=<?php echo $_GET['v']?>" method="post">
    <input type="text" name="delete-comment" value="<?php echo $entry['id']?>" hidden>
    <button class="button-icon bg-transparent" type="submit">
        <span class="material-icons">delete</span>
    </button>
</form>