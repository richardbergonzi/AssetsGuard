# Como Contribuir para o Asset Guard 🛡️

Nós adoramos receber contribuições da comunidade! Se você encontrou um bug, tem uma ideia de nova camada de segurança ou quer melhorar a performance do projeto, sinta-se em casa.

Este projeto segue o modelo tradicional de **Pull Requests** do Github. Você não precisa pedir autorização oficial para editar o seu clone e testar as coisas — mostre-nos pelo código!

## 🔄 Fluxo de Contribuição:

1. Faça um **Fork** deste repositório para o seu Github.
2. Crie uma branch com a sua nova funcionalidade ou correção:
   `git checkout -b feature/minha-super-protecao`
3. Faça as alterações no código de forma limpa.
   - *Por favor, tente seguir o estilo de código atual, tipagem PHP nativa e certifique-se de que nada quebra no ambiente isolado (Standalone).*
4. Faça o **Commit** das suas mudanças com mensagens semânticas. Exemplo:
   `git commit -m "feat: adiciona camada XOR avançada de randomização no ResourceBoot"`
5. Envie (Push) para a sua branch clonada:
   `git push origin feature/minha-super-protecao`
6. Venha aqui na aba `Pull requests` do repositório original e abra a sua PR sugerindo as mudanças para nós!

## ⚠️ Regras Técnicas:
- **Segurança primeiro:** Qualquer função de bloqueio que possa causar loops em "produção" em navegadores regulares será estritamente analisada (Ex: certifique-se que o código JS Anti-DevTools não quebre em celulares mobile comuns).
- **Zero Dependências:** O Asset Guard precisa continuar sendo um script em PHP `Puro/Standalone`. Por favor, NÃO inclua Composer, npm, Webpacks ou Node.js como dependência imposta na raiz.

---

# Contributing to Asset Guard 🌎

We love receiving community contributions! If you found a bug, have an idea for a new security layer, or want to improve the project's performance, feel right at home.

This project follows the traditional GitHub **Pull Request** model. You don't need official authorization to edit your clone and test things — show us through the code!

## 🔄 Contribution Workflow:

1. **Fork** this repository.
2. Create a branch for your new feature or fix:
   `git checkout -b feature/my-super-protection`
3. Make your clean code changes.
   - *Please try to follow the current code style, native PHP typing, and ensure nothing breaks in the standalone environment.*
4. **Commit** your changes with semantic messages. Example:
   `git commit -m "feat: adds advanced randomization XOR layer to ResourceBoot"`
5. **Push** to your cloned branch:
   `git push origin feature/my-super-protection`
6. Come here to the `Pull requests` tab of the original repo and submit your PR!

## ⚠️ Technical Rules:
- **Security first:** Any blocking function that might cause loops in production on regular browsers will be strictly analyzed (E.g., ensure Anti-DevTools JS doesn't break normal mobile phones).
- **Zero Dependencies:** Asset Guard must remain pure, standalone PHP. Please do NOT mandate Composer, npm, Node.js, or external DB libraries as core dependencies.
