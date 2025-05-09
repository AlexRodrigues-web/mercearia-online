# 🛒 Mercearia Online

Este é o projeto final do curso **Web Development** da **Master D**, desenvolvido com o objetivo de aplicar na prática os conhecimentos adquiridos ao longo da formação. Trata-se de uma plataforma completa de e-commerce para uma mercearia, com funcionalidades para clientes e administradores.

---

## 📌 Funcionalidades Principais

### 🧑 Cliente
- Navegação por categorias de produtos (Hortifruti, Bebidas, Limpeza, Importados)
- Página inicial com carrossel, promoções semanais e novidades
- Adição de produtos ao carrinho (com unidade ou kg)
- Finalização da compra com:
  - Formulário completo de encomenda
  - Vários métodos de pagamento (Multibanco, MB Way, Cartão, PayPal, Dinheiro)
  - Validação de campos obrigatórios e confirmação
- Histórico de pedidos
- Página de perfil com edição de dados e upload de foto

### 🛠️ Administrador
- Acesso restrito via login (usuário: `alex@adm.com`)
- Painel de gestão com:
  - Produtos (criar, editar, excluir, ver stock)
  - Promoções (definição e exibição automática na home)
  - Pedidos (acompanhamento das vendas)
  - Utilizadores e funcionários
  - Níveis de acesso, permissões e cargos
  - Fornecedores e integração com sistema de stock
  - Relatórios e estatísticas (em progresso)

---

## 🛠️ Tecnologias Utilizadas

- **Frontend**
  - HTML5 e CSS3
  - Bootstrap 5
  - JavaScript (com máscaras, validações e SweetAlert2)
  - FontAwesome

- **Backend**
  - PHP 8.x
  - Padrão MVC
  - Sistema de rotas personalizadas
  - Proteção CSRF e controle de sessões

- **Base de Dados**
  - MySQL (com migrations via Phinx)
  - Tabelas principais: `usuarios`, `produtos`, `pedidos`, `categorias`, `fornecedores`, `promocao`, etc.

---


---

## ✅ Como Testar

1. Clonar ou extrair o projeto na pasta do seu servidor local (ex: `htdocs/mercearia` no XAMPP).
2. Criar a base de dados `mercearia` e importar o ficheiro `mercearia.sql` (ou `mercearia_dump_atualizado.sql`).
3. Atualizar as credenciais de conexão no ficheiro `.env` (caso necessário).
4. Aceder via navegador:

   - Página inicial (pública):  
     `http://localhost/PROJETO/Mercearia-main/`

   - Login administrador:  
     `http://localhost/PROJETO/Mercearia-main/login`  
     **Email:** alex@adm.com  
     **Senha:** 123456

   - Login cliente:  
     **Email:** paula@paula.com
     **Senha:** 123456

---

## 🔒 Segurança

- Sistema de autenticação por sessão
- Níveis de acesso: cliente, funcionário, administrador
- Verificação de CSRF em todos os formulários
- Upload seguro de imagens de perfil
- Rotas protegidas por tipo de utilizador

---

## 📈 Status do Projeto

✔️ Funcionalidade completa do cliente  
✔️ Sistema de encomendas com validação  
✔️ Painel de administração com gestão total  
🟡 Relatórios e estatísticas em implementação  
🟢 Layout 100% responsivo e acessível

---

## 📝 Contribuidores

- **Alex [ADM]** – Desenvolvedor principal  
- Projeto criado para **Master D – Web Development MD.Ejercicios(01)Pt TD001251(01)**

---

## 📬 Contacto

Caso tenha dúvidas ou sugestões, por favor entre em contacto via:  
📧 `alex@adm.com`

---

## 📜 Licença

Projeto criado exclusivamente para fins educacionais e avaliação final do curso.  
Todos os direitos reservados ao autor.

