<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require '../inc/language.php';
require_once 'inc/db.php';
is_admin();

if(isset($_POST['question'])){
	$question = htmlentities($_POST['question']);
	$answer = nl2br(htmlentities($_POST['answer']));

	if(!is_null($question)){
		// valid question
		if(!is_null($answer)){
			//valid answer
			$req = $pdo->prepare("INSERT INTO faq SET question = ?, answer = ?");
			$req->execute([$question, $answer]);

			$_SESSION['flash']['success'] = "Question added.";
			header("Location: faq.php");
			exit();
		}
	}
}

if(isset($_GET['del'])){
	$id = htmlentities($_GET['del']);

	$req = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
	$req->execute([$id]);

	if($req->rowCount() == 0){
		$_SESSION['flash']['danger'] = "ID Doesnt exist.";
		header("Location: faq.php");
		exit();
	}

	$req = $pdo->prepare("DELETE FROM faq WHERE id = ?");
	$req->execute([$id]);

	$_SESSION['flash']['warning'] = "Question successfully deleted.";
	header("Location: faq.php");
	exit();
}

if(isset($_POST['edit_id'])){
	$id = htmlentities($_POST['edit_id']);
	$question = htmlentities($_POST['edit_question']);
	$answer = nl2br(htmlentities($_POST['edit_answer']));

	$req = $pdo->prepare("SELECT * FROM faq WHERE id = ?");
	$req->execute([$id]);
	if($req->rowCount() == 0){
		$_SESSION['flash']['danger'] = "ID Doesnt exist.";
		header("Location: faq.php");
		exit();
	}

	// id exist
	$req = $pdo->prepare("UPDATE faq SET question = ?, answer = ? WHERE id = ?");
	$req->execute([$question, $answer, $id]);

	$_SESSION['flash']['success'] = "Question successfully updated.";
	header("Location: faq.php");
	exit();
}

require_once 'inc/header.php';

?>
<h1>FAQ - Admin</h1>
<hr />

<div class="row">
	<div class="col-md-6 col-xs-12">
		<h4>Question list</h4>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Question</th>
					<th>Answer</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$req = $pdo->query("SELECT * FROM faq ORDER BY id ASC");
				while($row = $req->fetch()) : ?>
					<tr>
						<td><?=$row->id ?></td>
						<td><?=$row->question ?></td>
						<td><?=$row->answer ?></td>
						<td><button class="btn btn-info" type="button" data-toggle="modal" data-target="#question_<?=$row->id?>">Update / Delete</button></td>
					</tr>
				<?php endwhile; ?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6 col-xs-12">
		<h4>Add a Question</h4>

		<form method="post">
			<div class="form-group">
				<label>Question</label>
				<input type="text" name="question" class="form-control">
			</div>

			<div class="form-group">
				<label>Answer</label>
				<input type="text" name="answer" class="form-control">
			</div>

			<button type="submit" class="btn btn-success">Submit</button>
		</form>

	</div>
</div>



<?php require 'inc/footer.php'; ?>

<?php
$req = $pdo->query("SELECT * FROM faq");
while($row = $req->fetch()): ?>
<div class="modal fade" id="question_<?=$row->id?>" role="dialog">
	<div class="modal-dialog">

	<!-- Modal content-->
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit - <?=$row->question?></h4>
			</div>

			<div class="modal-body">
				<form method="post" action="">
					<div class="form-group">
						<label>Question</label>
						<input type="text" name="edit_question" class="form-control" value="<?=$row->question?>">
						<input type="hidden" name="edit_id" value="<?=$row->id?>">
					</div>

					<div class="form-group">
						<label>Answer</label>
						<textarea class="form-control" name="edit_answer"><?=$row->answer?></textarea>
					</div>

					<a href="?del=<?=$row->id?>"><button type="button" class="btn btn-danger">Delete</button></a>
					<button type="submit" class="btn btn-success">Update</button>
				</form>
			</div>
		</div>
	</div>
</div>
<?php endwhile; ?>
