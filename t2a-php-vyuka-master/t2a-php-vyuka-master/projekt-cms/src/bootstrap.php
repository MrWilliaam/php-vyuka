<?php

declare(strict_types=1);

/**
 * Bootstrap – načte všechny třídy projektu.
 *
 * Na začátku každé PHP stránky stačí vložit:
 *   require_once __DIR__ . '/src/bootstrap.php';
 */

// Database
require_once __DIR__ . '/Database.php';

// DTO
require_once __DIR__ . '/DTO/UserDTO.php';
require_once __DIR__ . '/DTO/CategoryDTO.php';
require_once __DIR__ . '/DTO/TagDTO.php';
require_once __DIR__ . '/DTO/ArticleDTO.php';
require_once __DIR__ . '/DTO/CommentDTO.php';
require_once __DIR__ . '/DTO/EventDTO.php';

// Repositories
require_once __DIR__ . '/Repository/UserRepository.php';
require_once __DIR__ . '/Repository/CategoryRepository.php';
require_once __DIR__ . '/Repository/TagRepository.php';
require_once __DIR__ . '/Repository/ArticleRepository.php';
require_once __DIR__ . '/Repository/CommentRepository.php';
require_once __DIR__ . '/Repository/EventRepository.php';

// Auth (session-based přihlášení administrátorů)
require_once __DIR__ . '/Auth.php';

// Validator
require_once __DIR__ . '/Validator.php';
