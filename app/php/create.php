require_once __DIR__ . '/../init.php';

try {
    $novoUsuario = [
        'nome' => 'tutu',
        'email' => 'arthur@gmail.com',
        'data_nascimento' => '2009-08-27'
    ];

    $idNovoUsuario = create($pdo, 'alunos', $novoUsuario);

    echo 'Novo usuário inserido com o ID: '
        . htmlspecialchars((string) $idNovoUsuario, ENT_QUOTES, 'UTF-8');

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo 'Erro ao cadastrar usuário. Confira a tabela e a conexão com o banco.';
}
