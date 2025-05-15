<?php
// src/Controller/pages/StudentModuleQuizController.php

namespace Controller\pages;

use Middleware\AuthMiddleware;
use Repositories\ModuleQuizRepository;
use Repositories\QuizQuestionRepository;
use Repositories\QuizOptionRepository;
use Repositories\UserQuizSubmissionRepository; // você criará em breve
use PDO;
use Twig\Environment;

class StudentModuleQuizController
{
    private Environment $twig;
    private PDO $pdo;
    private ModuleQuizRepository $quizRepo;
    private QuizQuestionRepository $questionRepo;
    private QuizOptionRepository $optionRepo;
    private UserQuizSubmissionRepository $submissionRepo;

    public function __construct(Environment $twig, PDO $pdo)
    {
        AuthMiddleware::handle();

        $this->twig           = $twig;
        $this->pdo            = $pdo;
        $this->quizRepo       = new ModuleQuizRepository($pdo);
        $this->questionRepo   = new QuizQuestionRepository($pdo);
        $this->optionRepo     = new QuizOptionRepository($pdo);
        $this->submissionRepo = new UserQuizSubmissionRepository($pdo);
    }

    // 1) Exibe o quiz para o aluno
    public function show(int $moduleId, int $quizId): void
    {
        $quiz      = $this->quizRepo->findById($quizId);
        $questions = $this->questionRepo->findByQuizId($quizId);
        $options   = [];
        foreach ($questions as $q) {
            $options[$q->getId()] = $this->optionRepo->findByQuestionId($q->getId());
        }

        echo $this->twig->render('student/quizzes/show.twig', [
            'moduleId'  => $moduleId,
            'quiz'      => $quiz,
            'questions' => $questions,
            'options'   => $options,
        ]);
    }

    // 2) Processa respostas e calcula pontuação
    public function submit(int $moduleId, int $quizId): void
    {
        $userId    = $_SESSION['user']['id'];
        $questions = $this->questionRepo->findByQuizId($quizId);

        $correctCount = 0;
        foreach ($questions as $q) {
            $selected = $_POST['question_'.$q->getId()] ?? [];
            // para multiple choice, espere um array; para true_false, um valor único
            $correctOptions = array_filter(
                $this->optionRepo->findByQuestionId($q->getId()),
                fn($opt) => $opt->isCorrect()
            );
            $correctIds = array_map(fn($o) => $o->getId(), $correctOptions);

            $selectedIds = is_array($selected) ? $selected : [ $selected ];
            sort($correctIds); sort($selectedIds);
            if ($correctIds === $selectedIds) {
                $correctCount++;
            }
        }

        $total    = count($questions);
        $scorePct = $total ? (int) round($correctCount / $total * 100) : 0;

        // Salva submissão (você implementa UserQuizSubmissionRepository)
        $this->submissionRepo->saveSubmission($userId, $quizId, $scorePct);

        // Redireciona ao material do próximo módulo ou mostra resultado
        if ($scorePct >= $this->quizRepo->findById($quizId)->getMinScore()) {
            header("Location: /courses/module/{$moduleId}/materials");
        } else {
            $_SESSION['flash']['error'][] = "Você atingiu {$scorePct}%, precisa de {$this->quizRepo->findById($quizId)->getMinScore()}% para avançar.";
            header("Location: /courses/module/{$moduleId}/quizzes/{$quizId}");
        }
        exit;
    }
}
