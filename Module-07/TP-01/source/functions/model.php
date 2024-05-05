<?php
include_once 'security.php';
include_once 'service.php';

class UserManager {
    private $pdo;

    public function __construct() {
        $this->pdo = $this->dbConnect();
    }

    private function dbConnect() {
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=esiea_web', 'root', 'root');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(Exception $e) {
            throw new Exception('Erreur lors de la connexion à la base de données : ' . $e->getMessage());
        }
    }

    public function registerUser($nom, $prenom, $adresse, $email, $password, $confirmPassword) {
        //role 0 = admin, 1 = modérateur, 2 = utilisateur
        $defaultRole = 2;

        // Vérifier le jeton CSRF
        Security::verifyCsrfToken();

        try {
            // Vérifier si l'email existe déjà (Prévention d'injection SQL)
            $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                // L'email existe déjà, renvoyer une erreur
                return "email_exists";
            } elseif ($password !== $confirmPassword) {
                // Les mots de passe ne correspondent pas, renvoyer une erreur
                return "password_mismatch";
            } else {
                // Enregistrement réussi
                $hashedPassword = hash('sha512', $password);
                $stmt = $this->pdo->prepare("INSERT INTO utilisateurs (nom, prenom, adresse, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $adresse, $email, $hashedPassword, $defaultRole]);
                return true;
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
        }
    }

    public function loginUser($email, $password) {
        try {
            // Récupérer les informations de l'utilisateur (Requête préparée pour prévenir l'injection SQL)
            $stmt = $this->pdo->prepare("SELECT id, email, password FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && hash('sha512', $password) == $user['password']) {
                // Connexion réussie, stocker l'ID de l'utilisateur en session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];

                return true;
            } else {
                // Échec de connexion, afficher un message d'erreur
                return "wrong_email_password";
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la connexion de l'utilisateur : " . $e->getMessage());
        }
    }

    public function getUserInfos($id) {
        try {
            // Récupérer les informations de l'utilisateur par son ID (Requête préparée pour prévenir l'injection SQL)
            $stmt = $this->pdo->prepare("SELECT id, nom, prenom, adresse, email FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            $userInfo = $stmt->fetch();

            return $userInfo;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération des informations de l'utilisateur : " . $e->getMessage());
        }
    }

    public function updateUserInfo($id, $nom, $prenom, $adresse, $email, $password, $confirmPassword) {
        Security::verifyCsrfToken();

        try {
            if ($email === $_SESSION['email']) {
                if ($password !== $confirmPassword) {
                    return "password_mismatch";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, adresse = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$nom, $prenom, $adresse, $email, $hashedPassword, $id]);
                    return true;
                }
            } else {
                $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $existingUser = $stmt->fetch();

                if ($existingUser) {
                    return "email_exists";
                } elseif ($password !== $confirmPassword) {
                    return "password_mismatch";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, adresse = ?, email = ?, password = ? WHERE id = ?");
                    $stmt->execute([$nom, $prenom, $adresse, $email, $hashedPassword, $id]);
                    $_SESSION['email'] = $email;
                    return true;
                }
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la modification des informations de l'utilisateur : " . $e->getMessage());
        }
    }

    public function closeAccount($id) {
        try {
            // Supprimer le compte de l'utilisateur (Requête préparée pour prévenir l'injection SQL)
            $stmt = $this->pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);

            // Déconnecter l'utilisateur et rediriger vers la page d'accueil
            session_destroy();
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la fermeture du compte de l'utilisateur : " . $e->getMessage());
        }
    }
}
?>