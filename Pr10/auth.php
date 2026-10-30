<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER["HTTPS"])
        && $_SERVER["HTTPS"] !== "off";

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/PR10",
        "secure" => $secure,
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}

require_once __DIR__ . "/db.php";

const SESSION_TIMEOUT = 900;
const REMEMBER_COOKIE = "studenthub_remember";

function h($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

function clearRememberCookie() {
    global $conn;

    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $hash = hash(
            "sha256",
            $_COOKIE[REMEMBER_COOKIE]
        );

        $stmt = $conn->prepare(
            "DELETE FROM remember_tokens WHERE token_hash = ?"
        );

        if ($stmt) {
            $stmt->bind_param("s", $hash);
            $stmt->execute();
            $stmt->close();
        }
    }

    setcookie(REMEMBER_COOKIE, "", [
        "expires" => time() - 3600,
        "path" => "/PR10",
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);

    unset($_COOKIE[REMEMBER_COOKIE]);
}

function issueRememberToken($userId) {
    global $conn;

    clearRememberCookie();

    $token = bin2hex(random_bytes(32));
    $hash = hash("sha256", $token);

    $expires = date(
        "Y-m-d H:i:s",
        time() + (30 * 24 * 60 * 60)
    );

    $stmt = $conn->prepare("
        INSERT INTO remember_tokens
        (user_id, token_hash, expires_at)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iss", $userId, $hash, $expires);
    $stmt->execute();
    $stmt->close();

    setcookie(REMEMBER_COOKIE, $token, [
        "expires" => time() + (30 * 24 * 60 * 60),
        "path" => "/PR10",
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"])
            && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);
}

function setAuthenticatedUser($user) {
    session_regenerate_id(true);

    $_SESSION["user_id"] = (int)$user["id"];
    $_SESSION["full_name"] = $user["full_name"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"] ?? "student";
    $_SESSION["last_activity"] = time();
}

function restoreRememberedUser() {
    global $conn;

    if (
        empty($_COOKIE[REMEMBER_COOKIE]) ||
        !empty($_SESSION["user_id"])
    ) {
        return;
    }

    $hash = hash(
        "sha256",
        $_COOKIE[REMEMBER_COOKIE]
    );

    $stmt = $conn->prepare("
        SELECT u.*
        FROM remember_tokens t
        JOIN users u ON u.id = t.user_id
        WHERE t.token_hash = ?
          AND t.expires_at > NOW()
        LIMIT 1
    ");

    $stmt->bind_param("s", $hash);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        clearRememberCookie();
        setAuthenticatedUser($user);
        issueRememberToken((int)$user["id"]);
    } else {
        clearRememberCookie();
    }
}

if (!empty($_SESSION["user_id"])) {
    $lastActivity = $_SESSION["last_activity"] ?? time();

    if (time() - $lastActivity > SESSION_TIMEOUT) {
        clearRememberCookie();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        session_start();

        $_SESSION["timed_out"] = true;
    } else {
        $_SESSION["last_activity"] = time();
    }
}

restoreRememberedUser();

function requireLogin() {
    if (empty($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if (($_SESSION["role"] ?? "student") !== $role) {
        http_response_code(403);

        exit(
            "403 Forbidden: You do not have permission " .
            "to access this page. " .
            "<a href='dashboard.php'>Go to dashboard</a>"
        );
    }
}
?>