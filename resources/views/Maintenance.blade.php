<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Em manutenção</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --muted-color: #6b7280;
            --bg-color: #f9fafb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: #111827;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            padding: 3rem 4rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 480px;
            width: 90%;
        }

        h1 {
            font-size: 4rem;
            font-weight: 800;
            color: var(--primary-color);
            margin: 0 0 0.5rem;
        }

        h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 1rem;
        }

        p {
            color: var(--muted-color);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        svg {
            width: 1.25rem;
            height: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>&#128295;</h1>
        <h2>Voltamos em breve!</h2>
        <p>Estamos realizando atualizações no sistema. Tente novamente mais tarde.</p>
    </div>
</body>
</html>
