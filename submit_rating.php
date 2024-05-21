<?php
session_start(); // Asegúrate de iniciar la sesión al principio del archivo

$connect = new PDO("mysql:host=localhost;dbname=sistema-resenas", "root", "");

// Enviar comentario
if (isset($_POST["rating_data"]) && isset($_SESSION['UserType']) && $_SESSION['UserType'] == 'User') {
	$data = array(
		':user_name'    =>  $_POST["user_name"],
		':user_rating'  =>  $_POST["rating_data"],
		':user_review'  =>  $_POST["user_review"],
		':datetime'     =>  time()
	);

	$query = "
    INSERT INTO review_table 
    (user_name, user_rating, user_review, datetime) 
    VALUES (:user_name, :user_rating, :user_review, :datetime)
    ";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	echo "Tu reseña ha sido registrada correctamente, muchas gracias !!";
} else if (isset($_POST["rating_data"])) {
	echo "Error: No se pudo enviar la reseña. Asegúrate de haber iniciado sesión como usuario.";
}

// Obtener comentarios
if (isset($_POST["action"])) {
	$average_rating = 0;
	$total_review = 0;
	$five_star_review = 0;
	$four_star_review = 0;
	$three_star_review = 0;
	$two_star_review = 0;
	$one_star_review = 0;
	$total_user_rating = 0;
	$review_content = array();

	$query = "
    SELECT * FROM review_table 
    ORDER BY review_id DESC
    ";

	$result = $connect->query($query, PDO::FETCH_ASSOC);

	foreach ($result as $row) {
		$review_content[] = array(
			'user_name'     =>  $row["user_name"],
			'user_review'   =>  $row["user_review"],
			'rating'        =>  $row["user_rating"],
			'datetime'      =>  date('l jS, F Y h:i:s A', $row["datetime"])
		);

		if ($row["user_rating"] == '5') {
			$five_star_review++;
		}

		if ($row["user_rating"] == '4') {
			$four_star_review++;
		}

		if ($row["user_rating"] == '3') {
			$three_star_review++;
		}

		if ($row["user_rating"] == '2') {
			$two_star_review++;
		}

		if ($row["user_rating"] == '1') {
			$one_star_review++;
		}

		$total_review++;
		$total_user_rating += $row["user_rating"];
	}

	if ($total_review > 0) {
		$average_rating = $total_user_rating / $total_review;
	}

	$output = array(
		'average_rating'    =>  number_format($average_rating, 1),
		'total_review'      =>  $total_review,
		'five_star_review'  =>  $five_star_review,
		'four_star_review'  =>  $four_star_review,
		'three_star_review' =>  $three_star_review,
		'two_star_review'   =>  $two_star_review,
		'one_star_review'   =>  $one_star_review,
		'review_data'       =>  $review_content
	);

	echo json_encode($output);
}
