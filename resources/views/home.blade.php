<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Bem vindo</h1>
        <a href="{{ route('books.index') }}" class="btn btn-primary">View Books</a>
    </div>
</body>
</html>
