import{r as a,c as x,o as n,w as b,a as r,j as _,f,b as i,g as d,v as u,t as m,e as v,d as y}from"./app-ZsQTxRhI.js";import{A as k}from"./AdminLayout-CXcIOg2Y.js";import{M as w}from"./MarkdownEditor-CjGFnrUh.js";import{_ as V}from"./_plugin-vue_export-helper-DlAUqK2U.js";const E={components:{AdminLayout:k,MarkdownEditor:w},props:{post:Object},setup(s){const t=y.useForm({uuid:s.post.uuid,title:s.post.title,content:s.post.content,excerpt:s.post.excerpt});function l(){t.put(route("posts.update",{uuid:s.post.uuid}))}return{form:t,submit:l}}},g={class:"mt-4 form-group"},M={key:0},A={class:"mt-3 form-group"},B={key:0},C={class:"mt-3 form-group"},L={key:0},N=["disabled"];function U(s,t,l,o,j,D){const c=a("MarkdownEditor"),p=a("AdminLayout");return n(),x(p,null,{default:b(()=>[t[7]||(t[7]=r("h1",null,"Editar Post",-1)),r("form",{onSubmit:t[3]||(t[3]=_((...e)=>o.submit&&o.submit(...e),["prevent"]))},[r("div",g,[t[4]||(t[4]=r("label",{for:"title"},"Título:",-1)),f(r("input",{id:"title","onUpdate:modelValue":t[0]||(t[0]=e=>o.form.title=e),class:"form-control",type:"text"},null,512),[[u,o.form.title]]),o.form.errors.title?(n(),i("div",M,m(o.form.errors.title),1)):d("",!0)]),r("div",A,[t[5]||(t[5]=r("label",{for:"content"},"Conteúdo:",-1)),v(c,{modelValue:o.form.content,"onUpdate:modelValue":t[1]||(t[1]=e=>o.form.content=e)},null,8,["modelValue"]),o.form.errors.content?(n(),i("div",B,m(o.form.errors.content),1)):d("",!0)]),r("div",C,[t[6]||(t[6]=r("label",{for:"excerpt"},"Resumo:",-1)),f(r("textarea",{id:"excerpt","onUpdate:modelValue":t[2]||(t[2]=e=>o.form.excerpt=e),rows:"5",class:"form-control"},null,512),[[u,o.form.excerpt]]),o.form.errors.excerpt?(n(),i("div",L,m(o.form.errors.excerpt),1)):d("",!0)]),r("button",{type:"submit",class:"btn btn-primary mt-4",disabled:o.form.processing}," Atualizar ",8,N)],32)]),_:1})}const F=V(E,[["render",U]]);export{F as default};
