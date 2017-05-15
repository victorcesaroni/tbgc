<?php

require_once dirname(__FILE__) . '/includes.php';

function validar_tipo_onda($input) {
    if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input))
        return false;

    $input = db_quote($input);    
    $rows = db_select("SELECT cod FROM tipos_onda WHERE cod = '$input'");

    if ($rows == false)
        return false;
    
    return true;
}

function validar_gramatura($input) {
    if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input) || $input <= 0)
        return false;

    return true;
}

function validar_tipo_papelao($input) {
    if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input))
        return false;
    
    $input = db_quote($input);
    $rows = db_select("SELECT cod FROM tipos_papelao WHERE cod = '$input'");

    if ($rows == false)
        return false;

    return true;
}

function validar_comprimento($input) {
    if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input) || $input <= 0)
        return false;
    
    return true;
}

function validar_largura($input) {
   if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input) || $input <= 0)
        return false;
    
    return true;
}

function validar_quantidade($input) {
   if (empty($input) || is_null($input))
        return false;

    if (!is_numeric($input) || $input <= 0)
        return false;
    
    return true;
}

function inserir_chapa($cod_tipo_papelao, $cod_tipo_onda, $gramatura, $comprimento, $largura, $quantidade) {
    $rows = db_select("SELECT cod_material FROM chapas WHERE cod_tipo_onda='$cod_tipo_onda' AND cod_tipo_papelao='$cod_tipo_papelao' AND gramatura=$gramatura AND comprimento=$comprimento AND largura=$largura");

    if ($rows == false) {
        // não existe uma chapa igual cadastrada
        
        db_query("BEGIN");
        db_query("INSERT INTO materiais (quantidade) VALUES ($quantidade)");
        $cod_material = db_insert_id();
        db_query("INSERT INTO chapas (cod_material,cod_tipo_papelao,cod_tipo_onda,gramatura,comprimento,largura) VALUES ($cod_material,$cod_tipo_papelao,$cod_tipo_onda,$gramatura,$comprimento,$largura)");
        db_query("COMMIT");

        return true;

    } else {
        // já existe uma chapa igual cadastrada

        db_query("BEGIN");
        $cod_material = $rows[0]['cod_material'];        
        db_query("UPDATE materiais SET quantidade=(quantidade + $quantidade) WHERE cod=$cod_material");
        db_query("COMMIT");

        return true;
    }

    return false;
}

function inserir_material_secundario($descricao, $quantidade) {
    $rows = db_select("SELECT cod_material FROM materiais_secundarios WHERE descricao='$descricao'");

    if ($rows == false) {
        // não existe uma chapa igual cadastrada
        
        db_query("BEGIN");
        db_query("INSERT INTO materiais (quantidade) VALUES ($quantidade)");
        $cod_material = db_insert_id();
        db_query("INSERT INTO materiais_secundarios (cod_material,descricao) VALUES ($cod_material,'$descricao')");
        db_query("COMMIT");

        return true;

    } else {
        // já existe uma chapa igual cadastrada

        db_query("BEGIN");
        $cod_material = $rows[0]['cod_material'];        
        db_query("UPDATE materiais SET quantidade=(quantidade + $quantidade) WHERE cod=$cod_material");
        db_query("COMMIT");

        return true;
    }

    return false;
}

$response = array();

$response['error'] = true;
$response['message'] = "";

if (isset($_POST['cadastrar'])) {
    $validado = true;
    $first = true;

    if (isset($_POST['tipo_material']) && $_POST['tipo_material'] === "chapa") {
        if (!(isset($_POST['tipo_onda']) && validar_tipo_onda($_POST['tipo_onda']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Tipo de onda inválido!";
        }

        if (!(isset($_POST['gramatura']) && validar_gramatura($_POST['gramatura']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Gramatura inválida!";
        }

        if (!(isset($_POST['tipo_papelao']) && validar_tipo_papelao($_POST['tipo_papelao']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Tipo de papelão inválido!";
        }

        if (!(isset($_POST['comprimento']) && validar_comprimento($_POST['comprimento']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Comprimento inválido!";
        }

        if (!(isset($_POST['largura']) && validar_largura($_POST['largura']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Largura inválida";
        }

        if (!(isset($_POST['quantidade']) && validar_quantidade($_POST['quantidade']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Quantidade inválida!";
        }
        
        if ($validado == false) {
            $response['error'] = true;
        } else {      
            $cod_tipo_papelao = $_POST['tipo_papelao'];
            $cod_tipo_onda = $_POST['tipo_onda'];
            $gramatura = $_POST['gramatura'];
            $largura = $_POST['largura'];
            $comprimento = $_POST['comprimento'];
            $quantidade = $_POST['quantidade'];
            
            if (inserir_chapa($cod_tipo_papelao, $cod_tipo_onda, $gramatura, $comprimento, $largura, $quantidade)) {
                $response['error'] = false;
                $response['message'] = "Material cadastrado com sucesso";
            } else {
                $response['error'] = true;
                $response['message'] = "ERRO FATAL: Material não foi cadastrado";
            }
        }
    } else if (isset($_POST['tipo_material']) && $_POST['tipo_material'] === "secundario") {
        if (!(isset($_POST['quantidade2']) && validar_quantidade($_POST['quantidade2']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Quantidade inválida!";
        }

        if (!(isset($_POST['descricao']))) {
            if ($first) { $first = false; } else { $response['message'] .= "<br>"; }
            $validado = false;
            $response['message'] .= "Descrição inválida!";
        }

        if ($validado == false) {
            $response['error'] = true;
        } else {
            $quantidade = $_POST['quantidade2'];
            $descricao = strtoupper(db_quote($_POST['descricao']));

            if (inserir_material_secundario($descricao, $quantidade)) {
                $response['error'] = false;
                $response['message'] = "Material cadastrado com sucesso";
            } else {
                $response['error'] = true;
                $response['message'] = "ERRO FATAL: Material não foi cadastrado";
            }
        }
    }
}

echo json_encode($response);

?>