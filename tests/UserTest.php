<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = $GLOBALS['pdo'];
        $this->resetDatabase();
    }

    private function resetDatabase(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS usuario");
        $this->pdo->exec("CREATE TABLE usuario (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            rut TEXT UNIQUE,
            nombre TEXT,
            password TEXT,
            rol TEXT,
            institucion_id INTEGER,
            fecha_habilitacion TEXT
        )");
    }

    private function createUser(string $rut, string $password, string $nombre = 'Test', string $rol = 'admin'): void
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuario (rut,nombre,password,rol,fecha_habilitacion) VALUES (:rut,:nombre,:pass,:rol,date('now'))");
        $stmt->execute(['rut'=>$rut,'nombre'=>$nombre,'pass'=>$hash,'rol'=>$rol]);
    }

    private function attemptLogin(string $rut, string $password): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE rut = :rut");
        $stmt->execute(['rut' => $rut]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function testLoginSuccess(): void
    {
        $this->createUser('12345678K', 'secret');
        $this->assertTrue($this->attemptLogin('12345678K', 'secret'));
    }

    public function testLoginFailure(): void
    {
        $this->createUser('12345678K', 'secret');
        $this->assertFalse($this->attemptLogin('12345678K', 'wrong'));
    }

    public function testCRUD(): void
    {
        // Create
        $this->createUser('11111111K', 'pw', 'A', 'admin');
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuario");
        $this->assertSame(1, (int)$stmt->fetchColumn());

        // Read
        $stmt = $this->pdo->prepare("SELECT nombre FROM usuario WHERE rut='11111111K'");
        $stmt->execute();
        $this->assertSame('A', $stmt->fetchColumn());

        // Update
        $stmt = $this->pdo->prepare("UPDATE usuario SET nombre='B' WHERE rut='11111111K'");
        $stmt->execute();
        $stmt = $this->pdo->prepare("SELECT nombre FROM usuario WHERE rut='11111111K'");
        $stmt->execute();
        $this->assertSame('B', $stmt->fetchColumn());

        // Delete
        $stmt = $this->pdo->prepare("DELETE FROM usuario WHERE rut='11111111K'");
        $stmt->execute();
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM usuario");
        $this->assertSame(0, (int)$stmt->fetchColumn());
    }
}
