import{r as f,b as i,o as e,e as p,a as o,s as v,j as g,f as n,g as l,v as d,t as m,F as u,d as b}from"./app-ZsQTxRhI.js";import{N as _}from"./Navbar-B2jyYuJF.js";/* empty css             */import{_ as w}from"./_plugin-vue_export-helper-DlAUqK2U.js";const N={components:{Navbar:_},setup(){const t=b.useForm({email:"",password:"",remember:!1});function a(){t.post("/login")}return{form:t,submit:a}}},k={class:"login-container"},V={class:"form-group"},y={key:0,class:"error"},L={class:"form-group"},h={key:0,class:"error"},x=["disabled"];function E(t,a,B,s,F,G){const c=f("Navbar");return e(),i(u,null,[p(c),o("div",k,[a[6]||(a[6]=o("header",{class:"login-header"},[o("h1",null,"Bem-vindo de Volta"),o("p",null,"Faça login para continuar no TechPulse")],-1)),o("form",{onSubmit:a[2]||(a[2]=g((...r)=>s.submit&&s.submit(...r),["prevent"]))},[o("div",V,[a[3]||(a[3]=o("label",{for:"email"},"E-mail",-1)),n(o("input",{id:"email","onUpdate:modelValue":a[0]||(a[0]=r=>s.form.email=r),type:"email",required:"",autofocus:""},null,512),[[d,s.form.email]]),s.form.errors.email?(e(),i("div",y,m(s.form.errors.email),1)):l("",!0)]),o("div",L,[a[4]||(a[4]=o("label",{for:"password"},"Senha",-1)),n(o("input",{id:"password","onUpdate:modelValue":a[1]||(a[1]=r=>s.form.password=r),type:"password",required:""},null,512),[[d,s.form.password]]),s.form.errors.password?(e(),i("div",h,m(s.form.errors.password),1)):l("",!0)]),a[5]||(a[5]=o("div",{class:"forgot-password"},[o("a",{href:"#"},"Esqueceu sua senha?")],-1)),o("button",{type:"submit",class:"btn",disabled:s.form.processing},"Entrar",8,x)],32),a[7]||(a[7]=v('<div class="social-login" data-v-cf4a5299><p data-v-cf4a5299>Ou faça login com:</p><div class="social-buttons" data-v-cf4a5299><a href="#" class="social-btn" data-v-cf4a5299><img src="/assets/img/social/google_ico.png" alt="Google" title="Login com Google" data-v-cf4a5299></a><a href="#" class="social-btn" data-v-cf4a5299><img src="/assets/img/social/github_ico.png" alt="GitHub" title="Login com GitHub" data-v-cf4a5299></a><a href="#" class="social-btn" data-v-cf4a5299><img src="/assets/img/social/linkedin_ico.png" alt="LinkedIn" title="Login com LinkedIn" data-v-cf4a5299></a></div></div><div class="signup-link" data-v-cf4a5299>Não tem uma conta? <a href="#" data-v-cf4a5299>Cadastre-se</a></div>',2))])],64)}const D=w(N,[["render",E],["__scopeId","data-v-cf4a5299"]]);export{D as default};
