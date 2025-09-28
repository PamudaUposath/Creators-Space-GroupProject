<?php
session_start();

echo "<h2>Quick Login Simulation</h2>";
echo "<p>Choose a user to simulate login:</p>";

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    switch ($user_id) {
        case 1:
            $_SESSION['user_id'] = 1;
            $_SESSION['role'] = 'admin';
            $_SESSION['first_name'] = 'Admin';
            $_SESSION['last_name'] = 'User';
            echo "<p>✅ Logged in as Admin User (can access both instructor and student messages)</p>";
            break;
        case 2:
            $_SESSION['user_id'] = 2;
            $_SESSION['role'] = 'instructor';
            $_SESSION['first_name'] = 'John';
            $_SESSION['last_name'] = 'Instructor';
            echo "<p>✅ Logged in as John Instructor</p>";
            break;
        case 14:
            $_SESSION['user_id'] = 14;
            $_SESSION['role'] = 'user';
            $_SESSION['first_name'] = 'Test';
            $_SESSION['last_name'] = 'User';
            echo "<p>✅ Logged in as Test User Student</p>";
            break;
    }
    
    echo "<h3>Current Session:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    
    echo '<p><a href="instructor-messages.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;">Instructor Messages</a>';
    echo '<a href="student-messages.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Student Messages</a></p>';
    echo '<p><a href="?' . '" style="color: #6c757d;">← Back to login selection</a></p>';
} else {
?>

<form method="post" style="margin: 20px 0;">
    <div style="margin: 10px 0;">
        <button type="submit" name="user_id" value="1" style="background: #dc3545; color: white; padding: 15px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px;">
            Login as Admin User (ID: 1)
        </button>
        <p style="color: #6c757d; margin: 5px 0; font-size: 14px;">Can access both instructor and student messages</p>
    </div>
    
    <div style="margin: 10px 0;">
        <button type="submit" name="user_id" value="2" style="background: #007bff; color: white; padding: 15px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px;">
            Login as John Instructor (ID: 2)
        </button>
        <p style="color: #6c757d; margin: 5px 0; font-size: 14px;">Access instructor messages</p>
    </div>
    
    <div style="margin: 10px 0;">
        <button type="submit" name="user_id" value="14" style="background: #28a745; color: white; padding: 15px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin: 5px;">
            Login as Test User Student (ID: 14)
        </button>
        <p style="color: #6c757d; margin: 5px 0; font-size: 14px;">Access student messages</p>
    </div>
</form>

<h3>Current Session Status:</h3>
<pre><?php print_r($_SESSION); ?></pre>

<?php } ?>