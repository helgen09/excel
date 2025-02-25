<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Импорт Excel</title>
    </head>
<body>
    <h1>Импорт Excel файла</h1>

    <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Загрузка</button>
    </form>

    @if (session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if (session('errors'))
        <div style="color: red;">
            <ul>
                @foreach (session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</body>
</html>