<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Post Published</title>
</head>

<body>
    <h1>A New Post Has Been Published!</h1>

    <h2>{{ $post->title }}</h2>

    @if ($post->short_description)
        <p>{{ $post->short_description }}</p>
    @endif

    <p>{{ $post->content }}</p>

    <p>
        <strong>Thank you for subscribing!</strong>
    </p>
</body>

</html>
