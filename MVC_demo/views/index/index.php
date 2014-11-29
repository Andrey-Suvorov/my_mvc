<div>
	<form method="post" action="index.php?route=handler" name="send" enctype="multipart/form-data">
		<fieldset>
			<legend>
				Форма для загрузки файла
			</legend>

			<p><input type="text" size="30" name="description" id="description" >
				<label for = "description">Описание</label></p>

			<p><input type="file" name="filename" ><br></p>

			<p><input id="submit" type="submit" value="Загрузить"></p>


		</fieldset>
	</form>
</div>

<?php if (isset($rows)) : ?>
	<ul>
		<?php foreach ($rows as $row) : ?>
		
			<li><span><?php echo $row['file_name'] . '.' . $row['ext_name']; ?></span>
				<form method="post" action="index.php?route=edit" name="form_edit">
					<input type="submit" value="удалить" name="delete">
					<input type="submit" value="изменить" name="edit">
					<input type="text" name="new_extension">
					<input type="hidden" value="<?php echo $row['ext_id']; ?>" name="ext_id">
					<input type="hidden" value="<?php echo $row['file_id']; ?>" name="file_id">
				</form>
			</li>	
		
		<?php endforeach; ?>
	</ul>
<?php endif; ?>