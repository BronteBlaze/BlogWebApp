<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta property="og:title" content="{{ $blog->title }}" />
  <meta property="og:description" content="{{ Str::limit($blog->description, 150) }}" />
  <meta property="og:image" content="{{ asset('storage/images/' . $blog->image) }}" />
  <meta property="og:url" content="{{ url('/blog/' . $blog->id) }}" />
  <meta property="og:type" content="article" />
  <title>{{ $blog->title }}</title>
</head>
<body>
  <p>Sharing blog {{ $blog->title }}</p>
</body>
</html>
