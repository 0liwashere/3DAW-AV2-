# Rent a Car

Sistema de aluguel de carros (parte do usuário) — projeto acadêmico.
HTML5, CSS3, JavaScript e PHP + MySQL.

## Como rodar (XAMPP)

1. Copie a pasta `rent-a-car` inteira para `htdocs` (ex: `C:\xampp\htdocs\rent-a-car`).
2. Abra o **phpMyAdmin**, crie o banco rodando o arquivo `sql/schema.sql`
   (ele já cria o banco `rent_a_car`, as tabelas e os dados iniciais: lojas, carros, proteções, adicionais e cupons).
3. Confira as credenciais em `includes/config.php` (por padrão, usuário `root` e senha vazia, padrão do XAMPP).
4. Inicie o Apache e o MySQL no painel do XAMPP.
5. Acesse `http://localhost/rent-a-car/index.php`.

## Status atual (o que já funciona)

- [x] Estrutura de pastas e banco de dados
- [x] Login (com sessão PHP) — `index.php` + `api/login.php`
- [x] Cadastro de usuário, salvando tudo no banco + upload da foto de habilitação — `cadastro.php` + `api/cadastro.php`
- [x] Home (busca, banner promocional, texto institucional, carrossel de turismo, lista de lojas) — `home.php`
- [x] Lista de Carros (com filtros) — `carros.php`
- [x] Detalhe do Carro + Reserva — `carro_detalhe.php`
- [x] Página de Pagamento (com cupom) — `pagamento.php`
- [x] Reserva Confirmada — `reserva_confirmada.php`
- [x] Perfil do usuário (visualizar e editar) — `perfil.php` + `api/atualizar_perfil.php`

**Todas as páginas da parte do usuário estão prontas! 🎉**

## Observações importantes

1. **Senha no cadastro:** o Figma que você me passou não tinha um campo de senha na tela
   de cadastro (só na tela de login). Como o login precisa de uma senha para autenticar,
   adicionei um campo "Senha de Acesso" (com confirmação) na tela de cadastro. Se preferir
   outra abordagem, me avisa que eu ajusto.

2. **Imagem do carro dourado na tela de Login:** essa imagem apareceu no seu print, mas
   você não chegou a me mandar o arquivo dela separadamente (só enviou a imagem de fundo
   do cadastro). Por enquanto deixei o lado direito do login com uma cor sólida escura —
   me manda o PNG dela (ou outra imagem de carro que preferir) que eu já encaixo.

3. **Login social (Google/Facebook) e login de administrador:** ficam apenas visuais por
   enquanto, sem funcionalidade, como combinamos.

4. **Imagens dos carros no banner "Black Novembro":** reaproveitei o Jeep Renegade e o
   Hyundai HB20 (já que você pediu pra reutilizar os carros existentes) no lugar do
   vermelho/branco originais do Figma. No segundo slide de exemplo usei BMW X3 e Kwid Zen.

5. **Botão "Pesquisar" da Home:** já leva para `carros.php` passando local/data como
   parâmetros de URL — mas essa página ainda não existe (é o próximo passo).

6. **Lista de Carros:** o Figma mostrava "105 carros encontrados" (número ilustrativo);
   como só temos 6 carros reais cadastrados no banco, o contador agora mostra a
   quantidade de verdade (que muda conforme os filtros aplicados). Os filtros de
   categoria, passageiros, câmbio, combustível e cor funcionam de verdade, consultando
   o banco. A "Capacidade de Bagagem" foi estimada a partir dos litros do porta-malas
   de cada carro (não temos um número exato de malas cadastrado). Também dá pra buscar
   por nome e ordenar por preço.

7. **Fluxo de Detalhe do Carro → Pagamento → Confirmação:** a seleção de proteção,
   adicionais, datas e lojas é calculada e guardada temporariamente na sessão PHP
   (`$_SESSION['reserva_pendente']`). Só quando o pagamento é confirmado (todos os
   campos do cartão preenchidos + os 2 aceites marcados) a reserva é de fato gravada
   nas tabelas `reservas` e `reserva_adicionais` do banco.

8. **Regra da promoção "Black Nov":** implementei como "a partir de 7 diárias, uma
   diária sai grátis" (o valor de uma diária é descontado do total). Se quiser uma
   regra diferente, me avisa que ajusto.

9. **Cupons e taxa de locação:** os cupons `ANDRE20VIP` e `BISPO010` já estão cadastrados
   no banco (tabela `cupons`) com os valores de desconto do seu Figma. A taxa de locação
   ficou fixa em R$ 100,29 para toda reserva (no seu Figma apareciam valores diferentes
   entre as duas telas de exemplo — assumi que era só ilustrativo).

10. **Adicionais com seletor de quantidade** (Cadeirinha Infantil, Locatário jovem):
    interpretei o seletor "+0, +1, +2, +3" como quantidade de itens, e o valor diário é
    multiplicado por essa quantidade. Se a intenção original era outra (ex: faixa etária
    do motorista jovem), me avisa que ajusto a lógica.

11. **Perfil do usuário:** o CPF fica bloqueado pra edição (não faz sentido alterar depois
    do cadastro). O campo CNH aparece vazio no primeiro acesso porque o cadastro só
    coletava a *foto* da habilitação, não o número — o usuário pode digitar o número
    manualmente aqui. A foto de perfil é opcional; se não for enviada, mostra um ícone
    padrão de usuário.
