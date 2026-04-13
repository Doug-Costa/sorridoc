# SorriDoc - Plataforma de Aprovação e Workflow

O SorriDoc é uma plataforma profissional de gestão de aprovações jurídicas e administrativas, desenvolvida para garantir integridade, segurança e agilidade em fluxos de decisão.

## Principais Funcionalidades

- **Fluxos de Aprovação Dinâmicos**: Suporte a aprovações simples ou duplas (Diretor + Advogada).
- **Assinatura Eletrônica com PIN**: Validação de identidade via código PIN criptografado para cada ação.
- **Certificados PDF com QR Code**: Cada aprovação gera um certificado formal com hash de integridade e QR Code para verificação pública.
- **Página de Veracidade**: Portal público para validar a autenticidade de documentos via hash SHA-256.
- **Conformidade LGPD**: Registros imutáveis de auditoria (Audit Log) e armazenamento seguro em disco privado.
- **Branding Profissional**: Interface customizada para ambientes médicos e jurídicos, removendo marcações de frameworks.

## Requisitos Técnicos

- **PHP**: 8.2+
- **Framework**: Laravel 11
- **Painel Administrativo**: FilamentPHP v3
- **Banco de Dados**: SQLite (MVP) ou MySQL
- **Extensões PHP**: `intl` (Obrigatória para o Filament/Laravel 11), `gd` (QR Code)
- **Dependências**: DomPDF (Certificados) e PHP-QRCode (Verificação)

## Configuração Local

1.  Clone o repositório.
2.  Execute `composer install`.
3.  Configure seu `.env` (certifique-se de definir `APP_URL` corretamente para o QR Code).
4.  Execute `php artisan migrate`.
5.  Crie um usuário no painel `/admin` ou via tinker.

---
*Este projeto foi desenvolvido para a DentalPress. Todos os direitos reservados.*
