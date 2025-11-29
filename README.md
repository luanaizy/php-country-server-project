
## Servidor REST em PHP - CountryLayer + Funcionalidade de Favoritos + dados em JSON,XML ou ProtoBuf

Este projeto implementa um servidor REST em PHP que consome a API CountryLayer e implementa um sistema de Salvamento de Favoritos em JSON.
Os dados podem ser retornados em JSON ou XML, e a lista completa de países pode ser retornada em Protocol Buffers.

# Como rodar o servidor

inicie o servidor php rodando, na pasta do projeto:
php -S localhost:8000 -t api

# Endpoints disponíveis

Listar todos os países

```bash
GET /api/countries
GET /api/countries?format=xml
GET /api/countries?format=json
GET /api/countries-proto
```

Listar país específico

```bash
GET /api/countries/{code}
GET /api/countries/{code}?format=xml
GET /api/countries/{code}?format=json
```
Exemplo:
```bash
GET /api/countries/BR?format=xml
```


Listar favoritos

```bash
GET /api/favorites
GET /api/favorites?format=xml
GET /api/favorites?format=json
```

Obter favorito específico
```bash
GET /api/favorites/{code}?format=json
```

Adicionar favorito
```bash
POST /api/favorites
Content-Type: application/json
```
ex: Body
```json
{
  "code": "BR",
  "name": "Brazil"
}
```

Remover favorito
```bash
DELETE /api/favorites/{code}
```
Exemplo:
```bash
DELETE /api/favorites/BR
```
