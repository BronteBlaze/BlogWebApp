import{u as p,a as v,j as e,af as b,b as f,y as u,p as N,r as i,g as y}from"./index-BzafhcuX.js";import{a as w}from"./Sidebar-DikHfiGF.js";const B=({blogId:s})=>{const t=p(),a=v(),r=()=>{t(b(s)),a(`/details/${s}`)};return e.jsx("div",{children:e.jsx("button",{type:"button",className:"text-red-color",onClick:r,children:"Read More"})})},C=({title:s,postDate:t,image:a,description:r,category:o,index:c,blogId:l})=>{const n=f(u),d=y(a),{first100Words:x,sanitizedDescription:m}=N(r),[g,h]=i.useState("");return i.useEffect(()=>{const j=x.replace(/<\/?[^>]+(>|$)/g,"");h(j)},[m]),e.jsxs("div",{className:"px-10 py-6 bg-white text-second-color border border-gray-300",children:[c>4&&!n&&e.jsx("div",{className:"absolute top-[80%] right-0 bottom-0 left-0 bg-gradient-to-t from-[rgba(255,255,255,1)] via-[rgba(255,255,255,0.7)] to-[rgba(255,255,255,0)]",children:e.jsx("div",{className:"invisible",children:"Oxford Blog"})}),e.jsxs("div",{className:"",children:[e.jsx("div",{className:"text-center text-sm text-red-color",children:e.jsx("h5",{children:o})}),e.jsx("div",{className:"text-center text-2xl text-heading-color font-semibold py-1",children:e.jsx("h2",{children:s})}),e.jsx("div",{className:"text-center text-sm text-red-color",children:e.jsx("span",{children:t})}),e.jsx("div",{className:"py-3",children:e.jsx("img",{src:d,alt:"oxford-blog-image",className:"h-[14rem] w-full object-cover"})}),e.jsxs("div",{className:"h-[12rem] break-words overflow-hidden",children:[g,"..."]}),e.jsxs("div",{className:"flex items-center justify-between mt-4",children:[e.jsx(B,{blogId:l}),e.jsx("div",{className:"text-xl",children:e.jsx(w,{})})]})]})]})};export{C as B};