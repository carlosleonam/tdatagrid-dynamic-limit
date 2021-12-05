# **LIMITE dinâmico para TDatagrid no Adianti Framework**
> Dynamic LIMIT for TDatagrid in the Adianti Framework
<!--
[![NPM Version][npm-image]][npm-url]
[![Build Status][travis-image]][travis-url]
[![Downloads Stats][npm-downloads]][npm-url]
 -->

![](github_cover.jpg)

O que é isso? Simples! Este é um seletor que permitirá aos usuários finais escolherem o número de linhas por página mostrada em uma classe TDatagrid.
>What is it? Simple! This is a selector that allows end users to choose the number of lines per page shown in a TDatagrid class.

Um cookie é usado para salvar a escolha do usuário..
>.A cookie is used to save the user's choice.

## **Instalação**
>Installation

### Composer:
```sh
composer require carlosleonam/tdatagrid_dynamic_limit
```

### Incluir no __libraries.html__ ou __libraries_user.html__:
```html
<!-- js-cookie CDN Files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
```

## **Uso**
>Use

Incluir o "use" no cabeçalho da classe
```php
<?php
use CarlosLeonam\TDatagridDynamicLimit\AdditionalFunctions;
```

No final da **"construct"** da classe, antes da linha **"parent::add($container);"**:
```php
$class_counter = __CLASS__ ;
include('vendor/carlosleonam/tdatagrid_dynamic_limit/src/include_counter.php');

$limit = CarlosLeonam\TDatagridDynamicLimit\AdditionalFunctions::checkCookieForLimit('profile_limit_'. self::$formName .'_per_page');
$this->limit = $limit;
```


