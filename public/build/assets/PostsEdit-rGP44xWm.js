import{r as l,c as p,o as i,w as c,a as e,b,d as v,e as d,f as m,v as k,t as a,g as y,i as _}from"./app-DrLxH_R1.js";import{A as w}from"./AdminLayout-Bjb_xwwo.js";import{M as x}from"./MarkdownEditor-O4Qv4XHt.js";import{_ as E}from"./_plugin-vue_export-helper-DlAUqK2U.js";const V={components:{AdminLayout:w,MarkdownEditor:x},props:{post:Object},setup(n){const t=_.useForm({title:n.post.title,content:n.post.content});function s(){t.put(route("posts.update",n.post))}return{form:t,submit:s}}},M={key:0},A={key:0},B=["disabled"];function C(n,t,s,o,g,L){const f=l("MarkdownEditor"),u=l("AdminLayout");return i(),p(u,null,{default:c(()=>[t[5]||(t[5]=e("h1",null,"Editar Post",-1)),e("form",{onSubmit:t[2]||(t[2]=b((...r)=>o.submit&&o.submit(...r),["prevent"]))},[e("div",null,[t[3]||(t[3]=e("label",{for:"title"},"Título:",-1)),v(e("input",{id:"title","onUpdate:modelValue":t[0]||(t[0]=r=>o.form.title=r),type:"text"},null,512),[[k,o.form.title]]),o.form.errors.title?(i(),d("div",M,a(o.form.errors.title),1)):m("",!0)]),e("div",null,[t[4]||(t[4]=e("label",{for:"content"},"Conteúdo:",-1)),y(f,{modelValue:o.form.content,"onUpdate:modelValue":t[1]||(t[1]=r=>o.form.content=r)},null,8,["modelValue"]),o.form.errors.content?(i(),d("div",A,a(o.form.errors.content),1)):m("",!0)]),e("button",{type:"submit",disabled:o.form.processing},"Atualizar",8,B)],32)]),_:1})}const T=E(V,[["render",C]]);export{T as default};
