<?php
class Bookmark
{
    public $visitCount;
    public $bookMarkName;
    public $Url;
}
/* start a php session once you launch landing page */
session_start();

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$parts = parse_url($actual_link);
$query = array();
if (!is_null($parts) && isset($parts['query'])) {
    parse_str($parts['query'], $query);
}
if (!isset($parts['query'])) {
    landingView();
} else if ($query["a"] == "Edit") {
    Edit($query["Id"]);
} else if ($query["a"] == "Delete") {
    Delete($query["Id"]);
}else if ($query["a"] == "GotoUrl") {
    $_SESSION["bookmarks"][$query["Id"]]->visitCount = $_SESSION["bookmarks"][$query["Id"]]->visitCount + 1;
        echo "<script>window.open('".$_SESSION["bookmarks"][$query["Id"]]->Url."','_blank');</script>";
    echo "<script>window.location.href='index.php';</script>";
}

if (!isset($_SESSION["bookmarks"]) && isset($bookmarks)) {
    $_SESSION["bookmarks"] = $bookmarks;
}
if (isset($_REQUEST["Create"])) {
    if (isset($_REQUEST["new_bookmark"]))
        addBookmark($_REQUEST["new_bookmark"]);
}
if (isset($_REQUEST["Update"])) {
    if (isset($_REQUEST["Url"]))
        UpdateBookMark($query["Id"], $_REQUEST["Url"]);
}
if (isset($_REQUEST["btnDelete"])) {
    DeleteBookMark($query["Id"]);
}
?>

<?php
/* function to link back to the landing view */
function landingView()
{

?>
    <!DOCTYPE html>
    <script>
        function Delete(id) {
            window.open("Index.php?a=Delete&Id=" + id, "_self");
        }

        function Edit(id) {
            window.open("Index.php?a=Edit&Id=" + id, "_self");
        }
    </script>
    <div style="border:1px solid black;width:3in;height:3in;margin:.05in;padding:0.25in;">
        <h1><a href="index.php">Bookmark Manager</a></h1>
        <div>
            <span style="color:gray">
                <form method="post">
                    <input type="text" style="width:1in;" placeholder="Title for Bookmark" name="new_bookmark">
                    <button type="submit" name="Create">Create Bookmark</button>
                </form>

            </span>
        </div>
        <h2>My Bookmarks</h2>
        <table>
            <tbody>
                <tr>
                    <th>Visits</th>
                    <th>Bookmark</th>
                    <th colspan="2">Actions</th>
                </tr>
                <?php
                $bookmarks = $_SESSION["bookmarks"];
                if (!is_null($bookmarks)) {
                    $count = 0;
                    foreach ($bookmarks as $book) {
                        echo "<tr><td>$book->visitCount </td><td><a href='index.php?a=GotoUrl&Id=".$count."'>$book->bookMarkName</a></td>
                    <td><button type='button' onclick='Edit(" . $count . ");'>Edit</button></td>
                    <td><button type='button' onclick='Delete(" . $count . ");'>Delete</button></td>
                    </tr>";
                        $count++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    </html>


<?php
}

//function to add a bookmark to the list of bookmarks
//gets the bookmark name and sets it as a new object in the arraylist
function addBookmark($new_bookmark_name)
{
    $bookmarks = array();
    $new_bookmark = $_POST['new_bookmark'];
    $Book = new Bookmark();
    $Book->Url = 'Mango';
    $Book->bookMarkName = $new_bookmark;
    $Book->visitCount = 0;

    if (isset($_SESSION["bookmarks"])) {
        $bookmarks = $_SESSION["bookmarks"];
    }
    if(!empty($_POST['new_bookmark']) && ctype_alnum($_POST['new_bookmark'])){
        array_push($bookmarks, $Book);
        $_SESSION["bookmarks"] = $bookmarks;
        //header("Location: index.php");
        //echo "<script>window.location.href='index.php';</script>";
         echo "<script>window.location.href='index.php?a=Edit&Id=".(count($bookmarks)-1)."';</script>";
    }
   // echo "<script>window.location.href='index.php?a=Edit&';</script>";
   
}


//function called when the user edits and saves the new bookmark
//function updates the bookmark url in the object varibles.
function UpdateBookMark($id, $Val)
{
    $bookmarks = $_SESSION["bookmarks"];
    $bookmark = $bookmarks[$id];
    $bookmark->Url = $Val;
    $bookmarks[$id] = $bookmark;
    $_SESSION["bookmarks"] = $bookmarks;
    
    $myfile =  fopen("url_files/".md5("index.php?a=GoToUrl&Id=".$Id).".txt", "w");
    fwrite($myfile, $Val);
    //header("Location: index.php");
    echo "<script>window.location.href='index.php';</script>";
}

//function called to when the user clicks confirm on the delete bookmark confirmation page
//removes the specific bookmark object from the list
function DeleteBookMark($id)
{
    $bookmarks = $_SESSION["bookmarks"];
    array_splice($bookmarks, $id, 1);
    $_SESSION["bookmarks"] = $bookmarks;
    echo "";
    //header("Location:index.php");
    //<?php header('Location: http://...'); >
	echo "<script>window.location.href='index.php';</script>";
}
?>

<?php
/* function that creates a new html view to the page (Edit Bookmark Page) 
   when the user clicks to edit the boookmark */
function Edit($Id)
{

?>
    <!DOCTYPE html>
    <div style="border:1px solid black;width:3in;height:3in;margin:.05in;padding:0.25in;">
        <h1><a href="index.php">Bookmark Manager</a></h1>
        <div>
            <form method="post">
                <h1>
                    <?php
                    echo $_SESSION["bookmarks"][$Id]->bookMarkName;
                    ?>
                </h1>
                <textarea placeholder="https://google.com" name="Url"></textarea>
                <button type="submit" name="Update">Save</button>
                <button type="button" onclick="window.open('index.php','_self')">Return</button>
            </form>
        </div>
    </div>

    </html>

<?php
}
?>



<?php
/*function that creates a new html view to the page (Edit Bookmark Page) 
   when the user clicks to edit the boookmark*/
function Delete($Id)
{

?>
    <!DOCTYPE html>
    <div style="border:1px solid black;width:4in;height:6in;margin:.05in;padding:0.25in;">
        <h1><a href="index.php">Bookmark Manager</a></h1>
        <div>
            <form method="post">
                <h1>
                    <?php
                    echo "Are you sure you want to delete the bookmark: " . $_SESSION["bookmarks"][$Id]->bookMarkName . "?";
                    ?>
                </h1>
                <button type="submit" name="btnDelete">Confirm</button>
                <button type="button" onclick="window.open('index.php','_self')">Cancel</button>
            </form>
        </div>
    </div>

    </html>

<?php
}
?>