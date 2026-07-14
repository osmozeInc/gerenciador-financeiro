<?php

class Controller {
    protected $idUsuarioLogado = 1;
    protected $mensagensModel = [
        'categoria' => [
            'salvar' => [
                'categoria_invalida' => [
                    'sucesso' => false,
                    'msgTipo' => 'warning',
                    'mensagem' => 'Tipo de categoria inválido!'
                ],
                'salvo_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Categoria cadastrada com sucesso!'
                ],
            ],
        ],
        'classesInvestimento' => [
            'selecionar_dados' => [
                'busca_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Dados do banco de dados carregados com sucesso!'
                ],
            ],
            'salvar' => [
                'salvo_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Tipo de conta salvo com sucesso!'
                ],
            ],
            'deletar' => []
        ],
        'contaMetodo' => [
            'salvar' => [
                'salvo_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Novo metodo salvo e disponível!'
                ],
            ],
        ],
        'transacao' => [
            'salvar' => [
                'salvo_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Transação salva com sucesso!'
                ],
                'cofre_invalido' => [
                    'sucesso' => false,
                    'msgTipo' => 'warning', 
                    'mensagem' => 'Selecione um cofre válido para a transação!'
                ],
            ],
        ],
        'genericas' => [
            'formulario_incompleto' => [
                'sucesso' => false,
                'msgTipo' => 'warning', 
                'mensagem' => 'Preencha todos os campos obrigatórios!'
            ],
            'erro_interno' => [
                'sucesso' => false,
                'msgTipo' => 'error', 
                'mensagem' => 'Erro interno ao salvar na base de dados.'
            ],
        ],
        'silenciosas' =>[
            'selecionar_dados' => [
                'busca_com_sucesso' => [
                    'sucesso' => true,
                    'msgTipo' => 'success', 
                    'mensagem' => 'Dados do banco de dados carregados com sucesso!'
                ],
                'busca_vazia' => [
                    'sucesso' => true,
                    'msgTipo' => 'warning',
                    'mensagem' => 'Nenhum dado encontrado.'
                ],
                'erro_interno' => [
                    'sucesso' => false,
                    'msgTipo' => 'error', 
                    'mensagem' => 'Erro interno ao buscar dados na base de dados.'
                ],
            ]
        ]
    ];

    protected function render($view) {

        $viewPath = __DIR__ . "/../Views/pages/{$view}.php";
        $layoutPath = __DIR__ . '/../Views/layout.php';

        if (!file_exists($viewPath)) {
            die("Erro de Arquitetura: A View '{$view}' não foi encontrada no sistema.");
        }

        if (file_exists($layoutPath)) {
            require_once $layoutPath;
        } else {
            die("Erro de Arquitetura: Arquivo de layout principal não encontrado.");
        }
    }
}