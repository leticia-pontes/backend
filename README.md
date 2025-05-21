# 🚀 Setup do Projeto 

## ✅ Requisitos do Sistema

- **PHP >= 8.1**
- **Composer**
- **MySQL**
- Extensões PHP obrigatórias:
  - `openssl`
  - `pdo`
  - `mbstring`
  - `tokenizer`
  - `bcmath`
  - `ctype`
  - `json`
  - `xml`
  - `fileinfo`
  - `curl`
  - `zip`

> 💡 Verifique as extensões com: `php -m` ou acesse o arquivo php.ini e busque por `extension=<extensao>` (se estiver com um `;` antes de `extension`, é porque está comentado).

---

## 📦 Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/leticia-pontes/backend
   cd backend
    ```

2. **Instale as dependências PHP**

   ```bash
   composer install
   ```

3. **Copie e configure o arquivo `.env`**

   ```bash
   cp .env.example .env
   ```

   > Edite as variáveis do banco de dados e da aplicação conforme seu ambiente.

4. **Gere a chave da aplicação**

   ```bash
   php artisan key:generate
   ```

5. **Execute as migrations**

   ```bash
   php artisan migrate
   ```

6. **Ajuste permissões (Linux/macOS)**

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
---

## ▶️ Executar o servidor local

```bash
php artisan serve
```

Acesse em: [http://localhost:8000](http://localhost:8000)

---

## 🧪 Swagger

1. Verifique se o pacote `l5-swagger` está instalado.
2. Gere a documentação:

   ```bash
   php artisan l5-swagger:generate
   ```

Acesse via: `http://localhost:8000/api/documentation`

---

## 🧹 Comandos úteis

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```
