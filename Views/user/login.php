<div id="form-container" class="centered">
    <form action="" method="post" id="form">
        <h2>Login</h2>
        <input type="text" name="name" placeholder="Name" required>
        <input type="password" name="pass" placeholder="Password" required>
        <input type="submit" value="Log in">
    </form>
</div>

<?php fillTemplate('default', ['title' => 'Login']) ?>