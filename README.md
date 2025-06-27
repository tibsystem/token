<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel 2

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Swagger API Documentation

Esta aplicação utiliza o pacote `l5-swagger` para gerar documentação no padrão OpenAPI.

1. Instale as dependências do PHP com Composer.
2. Execute `php artisan l5-swagger:generate` para gerar os arquivos de documentação.
3. A interface poderá ser acessada em `/api/documentation` após iniciar o servidor.

## Meta-transaction Relayer

Para que os investidores possam transferir tokens sem pagar gás, utilize o script `relay_meta_transfer.js`. Ele assina a intenção com a chave do investidor e envia a transação usando a carteira do proprietário.

```
node scripts/relay_meta_transfer.js <enderecoContrato> <caminhoABI> <chaveInvestidor> <chaveRelayer> <destino> <quantidade>
```

A variável de ambiente `POLYGON_RPC_URL` deve apontar para o nó da rede.

## Blockchain Purchase Flow

Quando um investidor realiza a rota `api/investments/purchase`, caso o imóvel
esteja tokenizado, os tokens são automaticamente transferidos na blockchain
utilizando a carteira do proprietário do contrato. O backend executa o script
`transfer_token.js` para mover os tokens do owner para o endereço do
<<<<<<< HEAD
investidor e registra o hash da transação nos logs de transações. A
quantidade informada na compra é convertida para base 18 antes de enviar a
transação para a rede.
=======
investidor e registra o hash da transação nos logs de transações.

## P2P Transaction Relayer

A rota `api/p2p/transactions` verifica o saldo em MATIC da carteira do
comprador. Se o saldo for zero, a transferência de tokens do vendedor para o
comprador é feita usando meta‑transação, assinada pelo vendedor e enviada pela
carteira do proprietário do contrato por meio do script `relay_meta_transfer.js`.
>>>>>>> origin/main
