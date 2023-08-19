function isCompatible(ua){return!!((function(){'use strict';return!this&&Function.prototype.bind;}())&&'querySelector'in document&&'localStorage'in window&&!ua.match(/MSIE 10|NetFront|Opera Mini|S40OviBrowser|MeeGo|Android.+Glass|^Mozilla\/5\.0 .+ Gecko\/$|googleweblight|PLAYSTATION|PlayStation/));}if(!isCompatible(navigator.userAgent)){document.documentElement.className=document.documentElement.className.replace(/(^|\s)client-js(\s|$)/,'$1client-nojs$2');while(window.NORLQ&&NORLQ[0]){NORLQ.shift()();}NORLQ={push:function(fn){fn();}};RLQ={push:function(){}};}else{if(window.performance&&performance.mark){performance.mark('mwStartup');}(function(){'use strict';var con=window.console;function logError(topic,data){var e=data.exception;var msg=(e?'Exception':'Error')+' in '+data.source+(data.module?' in module '+data.module:'')+(e?':':'.');con.log(msg);if(e){con.warn(e);}}function Map(){this.values=Object.create(null);}Map.prototype={constructor:Map,get:function(selection,fallback){if(
arguments.length<2){fallback=null;}if(typeof selection==='string'){return selection in this.values?this.values[selection]:fallback;}var results;if(Array.isArray(selection)){results={};for(var i=0;i<selection.length;i++){if(typeof selection[i]==='string'){results[selection[i]]=selection[i]in this.values?this.values[selection[i]]:fallback;}}return results;}if(selection===undefined){results={};for(var key in this.values){results[key]=this.values[key];}return results;}return fallback;},set:function(selection,value){if(arguments.length>1){if(typeof selection==='string'){this.values[selection]=value;return true;}}else if(typeof selection==='object'){for(var key in selection){this.values[key]=selection[key];}return true;}return false;},exists:function(selection){return typeof selection==='string'&&selection in this.values;}};var log=function(){};log.warn=Function.prototype.bind.call(con.warn,con);var mw={now:function(){var perf=window.performance;var navStart=perf&&perf.timing&&perf.timing.
navigationStart;mw.now=navStart&&perf.now?function(){return navStart+perf.now();}:Date.now;return mw.now();},trackQueue:[],track:function(topic,data){mw.trackQueue.push({topic:topic,data:data});},trackError:function(topic,data){mw.track(topic,data);logError(topic,data);},Map:Map,config:new Map(),messages:new Map(),templates:new Map(),log:log};window.mw=window.mediaWiki=mw;}());(function(){'use strict';var StringSet,store,hasOwn=Object.hasOwnProperty;function defineFallbacks(){StringSet=window.Set||function(){var set=Object.create(null);return{add:function(value){set[value]=true;},has:function(value){return value in set;}};};}defineFallbacks();function fnv132(str){var hash=0x811C9DC5;for(var i=0;i<str.length;i++){hash+=(hash<<1)+(hash<<4)+(hash<<7)+(hash<<8)+(hash<<24);hash^=str.charCodeAt(i);}hash=(hash>>>0).toString(36).slice(0,5);while(hash.length<5){hash='0'+hash;}return hash;}var isES6Supported=typeof Promise==='function'&&Promise.prototype.finally&&/./g.flags==='g'&&(function(){
try{new Function('(a = 0) => a');return true;}catch(e){return false;}}());var registry=Object.create(null),sources=Object.create(null),handlingPendingRequests=false,pendingRequests=[],queue=[],jobs=[],willPropagate=false,errorModules=[],baseModules=["jquery","mediawiki.base"],marker=document.querySelector('meta[name="ResourceLoaderDynamicStyles"]'),lastCssBuffer,rAF=window.requestAnimationFrame||setTimeout;function addToHead(el,nextNode){if(nextNode&&nextNode.parentNode){nextNode.parentNode.insertBefore(el,nextNode);}else{document.head.appendChild(el);}}function newStyleTag(text,nextNode){var el=document.createElement('style');el.appendChild(document.createTextNode(text));addToHead(el,nextNode);return el;}function flushCssBuffer(cssBuffer){if(cssBuffer===lastCssBuffer){lastCssBuffer=null;}newStyleTag(cssBuffer.cssText,marker);for(var i=0;i<cssBuffer.callbacks.length;i++){cssBuffer.callbacks[i]();}}function addEmbeddedCSS(cssText,callback){if(!lastCssBuffer||cssText.slice(0,7)===
'@import'){lastCssBuffer={cssText:'',callbacks:[]};rAF(flushCssBuffer.bind(null,lastCssBuffer));}lastCssBuffer.cssText+='\n'+cssText;lastCssBuffer.callbacks.push(callback);}function getCombinedVersion(modules){var hashes=modules.reduce(function(result,module){return result+registry[module].version;},'');return fnv132(hashes);}function allReady(modules){for(var i=0;i<modules.length;i++){if(mw.loader.getState(modules[i])!=='ready'){return false;}}return true;}function allWithImplicitReady(module){return allReady(registry[module].dependencies)&&(baseModules.indexOf(module)!==-1||allReady(baseModules));}function anyFailed(modules){for(var i=0;i<modules.length;i++){var state=mw.loader.getState(modules[i]);if(state==='error'||state==='missing'){return modules[i];}}return false;}function doPropagation(){var didPropagate=true;var module;while(didPropagate){didPropagate=false;while(errorModules.length){var errorModule=errorModules.shift(),baseModuleError=baseModules.indexOf(errorModule)!==-1;
for(module in registry){if(registry[module].state!=='error'&&registry[module].state!=='missing'){if(baseModuleError&&baseModules.indexOf(module)===-1){registry[module].state='error';didPropagate=true;}else if(registry[module].dependencies.indexOf(errorModule)!==-1){registry[module].state='error';errorModules.push(module);didPropagate=true;}}}}for(module in registry){if(registry[module].state==='loaded'&&allWithImplicitReady(module)){execute(module);didPropagate=true;}}for(var i=0;i<jobs.length;i++){var job=jobs[i];var failed=anyFailed(job.dependencies);if(failed!==false||allReady(job.dependencies)){jobs.splice(i,1);i-=1;try{if(failed!==false&&job.error){job.error(new Error('Failed dependency: '+failed),job.dependencies);}else if(failed===false&&job.ready){job.ready();}}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'load-callback'});}didPropagate=true;}}}willPropagate=false;}function setAndPropagate(module,state){registry[module].state=state;if(state==='ready'){
store.add(module);}else if(state==='error'||state==='missing'){errorModules.push(module);}else if(state!=='loaded'){return;}if(willPropagate){return;}willPropagate=true;mw.requestIdleCallback(doPropagation,{timeout:1});}function sortDependencies(module,resolved,unresolved){if(!(module in registry)){throw new Error('Unknown module: '+module);}if(typeof registry[module].skip==='string'){var skip=(new Function(registry[module].skip)());registry[module].skip=!!skip;if(skip){registry[module].dependencies=[];setAndPropagate(module,'ready');return;}}if(!unresolved){unresolved=new StringSet();}var deps=registry[module].dependencies;unresolved.add(module);for(var i=0;i<deps.length;i++){if(resolved.indexOf(deps[i])===-1){if(unresolved.has(deps[i])){throw new Error('Circular reference detected: '+module+' -> '+deps[i]);}sortDependencies(deps[i],resolved,unresolved);}}resolved.push(module);}function resolve(modules){var resolved=baseModules.slice();for(var i=0;i<modules.length;i++){
sortDependencies(modules[i],resolved);}return resolved;}function resolveStubbornly(modules){var resolved=baseModules.slice();for(var i=0;i<modules.length;i++){var saved=resolved.slice();try{sortDependencies(modules[i],resolved);}catch(err){resolved=saved;mw.log.warn('Skipped unavailable module '+modules[i]);if(modules[i]in registry){mw.trackError('resourceloader.exception',{exception:err,source:'resolve'});}}}return resolved;}function resolveRelativePath(relativePath,basePath){var relParts=relativePath.match(/^((?:\.\.?\/)+)(.*)$/);if(!relParts){return null;}var baseDirParts=basePath.split('/');baseDirParts.pop();var prefixes=relParts[1].split('/');prefixes.pop();var prefix;while((prefix=prefixes.pop())!==undefined){if(prefix==='..'){baseDirParts.pop();}}return(baseDirParts.length?baseDirParts.join('/')+'/':'')+relParts[2];}function makeRequireFunction(moduleObj,basePath){return function require(moduleName){var fileName=resolveRelativePath(moduleName,basePath);if(fileName===null){
return mw.loader.require(moduleName);}if(hasOwn.call(moduleObj.packageExports,fileName)){return moduleObj.packageExports[fileName];}var scriptFiles=moduleObj.script.files;if(!hasOwn.call(scriptFiles,fileName)){throw new Error('Cannot require undefined file '+fileName);}var result,fileContent=scriptFiles[fileName];if(typeof fileContent==='function'){var moduleParam={exports:{}};fileContent(makeRequireFunction(moduleObj,fileName),moduleParam,moduleParam.exports);result=moduleParam.exports;}else{result=fileContent;}moduleObj.packageExports[fileName]=result;return result;};}function addScript(src,callback){var script=document.createElement('script');script.src=src;script.onload=script.onerror=function(){if(script.parentNode){script.parentNode.removeChild(script);}if(callback){callback();callback=null;}};document.head.appendChild(script);return script;}function queueModuleScript(src,moduleName,callback){pendingRequests.push(function(){if(moduleName!=='jquery'){window.require=mw.loader.
require;window.module=registry[moduleName].module;}addScript(src,function(){delete window.module;callback();if(pendingRequests[0]){pendingRequests.shift()();}else{handlingPendingRequests=false;}});});if(!handlingPendingRequests&&pendingRequests[0]){handlingPendingRequests=true;pendingRequests.shift()();}}function addLink(url,media,nextNode){var el=document.createElement('link');el.rel='stylesheet';if(media){el.media=media;}el.href=url;addToHead(el,nextNode);return el;}function domEval(code){var script=document.createElement('script');if(mw.config.get('wgCSPNonce')!==false){script.nonce=mw.config.get('wgCSPNonce');}script.text=code;document.head.appendChild(script);script.parentNode.removeChild(script);}function enqueue(dependencies,ready,error){if(allReady(dependencies)){if(ready){ready();}return;}var failed=anyFailed(dependencies);if(failed!==false){if(error){error(new Error('Dependency '+failed+' failed to load'),dependencies);}return;}if(ready||error){jobs.push({dependencies:
dependencies.filter(function(module){var state=registry[module].state;return state==='registered'||state==='loaded'||state==='loading'||state==='executing';}),ready:ready,error:error});}dependencies.forEach(function(module){if(registry[module].state==='registered'&&queue.indexOf(module)===-1){queue.push(module);}});mw.loader.work();}function execute(module){if(registry[module].state!=='loaded'){throw new Error('Module in state "'+registry[module].state+'" may not execute: '+module);}registry[module].state='executing';var runScript=function(){var script=registry[module].script;var markModuleReady=function(){setAndPropagate(module,'ready');};var nestedAddScript=function(arr,offset){if(offset>=arr.length){markModuleReady();return;}queueModuleScript(arr[offset],module,function(){nestedAddScript(arr,offset+1);});};try{if(Array.isArray(script)){nestedAddScript(script,0);}else if(typeof script==='function'){if(module==='jquery'){script();}else{script(window.$,window.$,mw.loader.require,
registry[module].module);}markModuleReady();}else if(typeof script==='object'&&script!==null){var mainScript=script.files[script.main];if(typeof mainScript!=='function'){throw new Error('Main file in module '+module+' must be a function');}mainScript(makeRequireFunction(registry[module],script.main),registry[module].module,registry[module].module.exports);markModuleReady();}else if(typeof script==='string'){domEval(script);markModuleReady();}else{markModuleReady();}}catch(e){setAndPropagate(module,'error');mw.trackError('resourceloader.exception',{exception:e,module:module,source:'module-execute'});}};if(registry[module].messages){mw.messages.set(registry[module].messages);}if(registry[module].templates){mw.templates.set(module,registry[module].templates);}var cssPending=0;var cssHandle=function(){cssPending++;return function(){cssPending--;if(cssPending===0){var runScriptCopy=runScript;runScript=undefined;runScriptCopy();}};};var style=registry[module].style;if(style){if('css'in style
){for(var i=0;i<style.css.length;i++){addEmbeddedCSS(style.css[i],cssHandle());}}if('url'in style){for(var media in style.url){var urls=style.url[media];for(var j=0;j<urls.length;j++){addLink(urls[j],media,marker);}}}}if(module==='user'){var siteDeps;var siteDepErr;try{siteDeps=resolve(['site']);}catch(e){siteDepErr=e;runScript();}if(!siteDepErr){enqueue(siteDeps,runScript,runScript);}}else if(cssPending===0){runScript();}}function sortQuery(o){var sorted={};var list=[];for(var key in o){list.push(key);}list.sort();for(var i=0;i<list.length;i++){sorted[list[i]]=o[list[i]];}return sorted;}function buildModulesString(moduleMap){var str=[];var list=[];var p;function restore(suffix){return p+suffix;}for(var prefix in moduleMap){p=prefix===''?'':prefix+'.';str.push(p+moduleMap[prefix].join(','));list.push.apply(list,moduleMap[prefix].map(restore));}return{str:str.join('|'),list:list};}function makeQueryString(params){var str='';for(var key in params){str+=(str?'&':'')+encodeURIComponent(key
)+'='+encodeURIComponent(params[key]);}return str;}function batchRequest(batch){if(!batch.length){return;}var sourceLoadScript,currReqBase,moduleMap;function doRequest(){var query=Object.create(currReqBase),packed=buildModulesString(moduleMap);query.modules=packed.str;query.version=getCombinedVersion(packed.list);query=sortQuery(query);addScript(sourceLoadScript+'?'+makeQueryString(query));}batch.sort();var reqBase={"lang":"en","skin":"vector"};var splits=Object.create(null);for(var b=0;b<batch.length;b++){var bSource=registry[batch[b]].source;var bGroup=registry[batch[b]].group;if(!splits[bSource]){splits[bSource]=Object.create(null);}if(!splits[bSource][bGroup]){splits[bSource][bGroup]=[];}splits[bSource][bGroup].push(batch[b]);}for(var source in splits){sourceLoadScript=sources[source];for(var group in splits[source]){var modules=splits[source][group];currReqBase=Object.create(reqBase);if(group===0&&mw.config.get('wgUserName')!==null){currReqBase.user=mw.config.get('wgUserName');}
var currReqBaseLength=makeQueryString(currReqBase).length+23;var length=0;moduleMap=Object.create(null);for(var i=0;i<modules.length;i++){var lastDotIndex=modules[i].lastIndexOf('.'),prefix=modules[i].slice(0,Math.max(0,lastDotIndex)),suffix=modules[i].slice(lastDotIndex+1),bytesAdded=moduleMap[prefix]?suffix.length+3:modules[i].length+3;if(length&&length+currReqBaseLength+bytesAdded>mw.loader.maxQueryLength){doRequest();length=0;moduleMap=Object.create(null);}if(!moduleMap[prefix]){moduleMap[prefix]=[];}length+=bytesAdded;moduleMap[prefix].push(suffix);}doRequest();}}}function asyncEval(implementations,cb){if(!implementations.length){return;}mw.requestIdleCallback(function(){try{domEval(implementations.join(';'));}catch(err){cb(err);}});}function getModuleKey(module){return module in registry?(module+'@'+registry[module].version):null;}function splitModuleKey(key){var index=key.lastIndexOf('@');if(index===-1||index===0){return{name:key,version:''};}return{name:key.slice(0,index),
version:key.slice(index+1)};}function registerOne(module,version,dependencies,group,source,skip){if(module in registry){throw new Error('module already registered: '+module);}version=String(version||'');if(version.slice(-1)==='!'){if(!isES6Supported){return;}version=version.slice(0,-1);}registry[module]={module:{exports:{}},packageExports:{},version:version,dependencies:dependencies||[],group:typeof group==='undefined'?null:group,source:typeof source==='string'?source:'local',state:'registered',skip:typeof skip==='string'?skip:null};}mw.loader={moduleRegistry:registry,maxQueryLength:2000,addStyleTag:newStyleTag,addScriptTag:addScript,addLinkTag:addLink,enqueue:enqueue,resolve:resolve,work:function(){store.init();var q=queue.length,storedImplementations=[],storedNames=[],requestNames=[],batch=new StringSet();while(q--){var module=queue[q];if(mw.loader.getState(module)==='registered'&&!batch.has(module)){registry[module].state='loading';batch.add(module);var implementation=store.get(
module);if(implementation){storedImplementations.push(implementation);storedNames.push(module);}else{requestNames.push(module);}}}queue=[];asyncEval(storedImplementations,function(err){store.stats.failed++;store.clear();mw.trackError('resourceloader.exception',{exception:err,source:'store-eval'});var failed=storedNames.filter(function(name){return registry[name].state==='loading';});batchRequest(failed);});batchRequest(requestNames);},addSource:function(ids){for(var id in ids){if(id in sources){throw new Error('source already registered: '+id);}sources[id]=ids[id];}},register:function(modules){if(typeof modules!=='object'){registerOne.apply(null,arguments);return;}function resolveIndex(dep){return typeof dep==='number'?modules[dep][0]:dep;}for(var i=0;i<modules.length;i++){var deps=modules[i][2];if(deps){for(var j=0;j<deps.length;j++){deps[j]=resolveIndex(deps[j]);}}registerOne.apply(null,modules[i]);}},implement:function(module,script,style,messages,templates){var split=splitModuleKey
(module),name=split.name,version=split.version;if(!(name in registry)){mw.loader.register(name);}if(registry[name].script!==undefined){throw new Error('module already implemented: '+name);}if(version){registry[name].version=version;}registry[name].script=script||null;registry[name].style=style||null;registry[name].messages=messages||null;registry[name].templates=templates||null;if(registry[name].state!=='error'&&registry[name].state!=='missing'){setAndPropagate(name,'loaded');}},load:function(modules,type){if(typeof modules==='string'&&/^(https?:)?\/?\//.test(modules)){if(type==='text/css'){addLink(modules);}else if(type==='text/javascript'||type===undefined){addScript(modules);}else{throw new Error('Invalid type '+type);}}else{modules=typeof modules==='string'?[modules]:modules;enqueue(resolveStubbornly(modules));}},state:function(states){for(var module in states){if(!(module in registry)){mw.loader.register(module);}setAndPropagate(module,states[module]);}},getState:function(module){
return module in registry?registry[module].state:null;},require:function(moduleName){if(mw.loader.getState(moduleName)!=='ready'){throw new Error('Module "'+moduleName+'" is not loaded');}return registry[moduleName].module.exports;}};var hasPendingWrites=false;function flushWrites(){store.prune();while(store.queue.length){store.set(store.queue.shift());}try{localStorage.removeItem(store.key);var data=JSON.stringify(store);localStorage.setItem(store.key,data);}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-update'});}hasPendingWrites=false;}mw.loader.store=store={enabled:null,items:{},queue:[],stats:{hits:0,misses:0,expired:0,failed:0},toJSON:function(){return{items:store.items,vary:store.vary,asOf:Math.ceil(Date.now()/1e7)};},key:"MediaWikiModuleStore:logi_wiki",vary:"vector:1:en",init:function(){if(this.enabled===null){this.enabled=false;if(true){this.load();}else{this.clear();}}},load:function(){try{var raw=localStorage.getItem(this.key);
this.enabled=true;var data=JSON.parse(raw);if(data&&data.vary===this.vary&&data.items&&Date.now()<(data.asOf*1e7)+259e7){this.items=data.items;}}catch(e){}},get:function(module){if(this.enabled){var key=getModuleKey(module);if(key in this.items){this.stats.hits++;return this.items[key];}this.stats.misses++;}return false;},add:function(module){if(this.enabled){this.queue.push(module);this.requestUpdate();}},set:function(module){var args,encodedScript,descriptor=registry[module],key=getModuleKey(module);if(key in this.items||!descriptor||descriptor.state!=='ready'||!descriptor.version||descriptor.group===1||descriptor.group===0||[descriptor.script,descriptor.style,descriptor.messages,descriptor.templates].indexOf(undefined)!==-1){return;}try{if(typeof descriptor.script==='function'){encodedScript=String(descriptor.script);}else if(typeof descriptor.script==='object'&&descriptor.script&&!Array.isArray(descriptor.script)){encodedScript='{'+'main:'+JSON.stringify(descriptor.script.main)+','
+'files:{'+Object.keys(descriptor.script.files).map(function(file){var value=descriptor.script.files[file];return JSON.stringify(file)+':'+(typeof value==='function'?value:JSON.stringify(value));}).join(',')+'}}';}else{encodedScript=JSON.stringify(descriptor.script);}args=[JSON.stringify(key),encodedScript,JSON.stringify(descriptor.style),JSON.stringify(descriptor.messages),JSON.stringify(descriptor.templates)];}catch(e){mw.trackError('resourceloader.exception',{exception:e,source:'store-localstorage-json'});return;}var src='mw.loader.implement('+args.join(',')+');';if(src.length>1e5){return;}this.items[key]=src;},prune:function(){for(var key in this.items){if(getModuleKey(splitModuleKey(key).name)!==key){this.stats.expired++;delete this.items[key];}}},clear:function(){this.items={};try{localStorage.removeItem(this.key);}catch(e){}},requestUpdate:function(){if(!hasPendingWrites){hasPendingWrites=true;setTimeout(function(){mw.requestIdleCallback(flushWrites);},2000);}}};}());mw.
requestIdleCallbackInternal=function(callback){setTimeout(function(){var start=mw.now();callback({didTimeout:false,timeRemaining:function(){return Math.max(0,50-(mw.now()-start));}});},1);};mw.requestIdleCallback=window.requestIdleCallback?window.requestIdleCallback.bind(window):mw.requestIdleCallbackInternal;(function(){var queue;mw.loader.addSource({"local":"/load.php"});mw.loader.register([["site","cegdc",[1]],["site.styles","1xdqj",[],2],["filepage","1ljys"],["user","1tdkc",[],0],["user.styles","18fec",[],0],["user.options","12s5i",[],1],["mediawiki.skinning.interface","36jfo"],["jquery.makeCollapsible.styles","qx5d5"],["mediawiki.skinning.content.parsoid","mj6zp"],["jquery","p9z7x"],["es6-polyfills","1ihgd",[],null,null,"return Array.prototype.find\u0026\u0026Array.prototype.findIndex\u0026\u0026Array.prototype.includes\u0026\u0026typeof Promise==='function'\u0026\u0026Promise.prototype.finally;"],["web2017-polyfills","5cxhc",[10],null,null,
"return'IntersectionObserver'in window\u0026\u0026typeof fetch==='function'\u0026\u0026typeof URL==='function'\u0026\u0026'toJSON'in URL.prototype;"],["mediawiki.base","77zkn",[9]],["jquery.chosen","fjvzv"],["jquery.client","1jnox"],["jquery.color","1y5ur"],["jquery.confirmable","gjfq6",[104]],["jquery.cookie","emj1l"],["jquery.form","1djyv"],["jquery.fullscreen","1lanf"],["jquery.highlightText","a2wnf",[80]],["jquery.hoverIntent","1cahm"],["jquery.i18n","1pu0k",[103]],["jquery.lengthLimit","k5zgm",[64]],["jquery.makeCollapsible","1863g",[7,80]],["jquery.spinner","9br9t",[26]],["jquery.spinner.styles","153wt"],["jquery.suggestions","1g6wh",[20]],["jquery.tablesorter","oqg5p",[29,105,80]],["jquery.tablesorter.styles","vfgav"],["jquery.textSelection","m1do8",[14]],["jquery.tipsy","10wlp"],["jquery.ui","1cfzx"],["moment","x1k6h",[101,80]],["vue","eyq5j!"],["@vue/composition-api","scw0q!",[34]],["vuex","1twvy!",[34]],["@wikimedia/codex","1isdg!",[34]],["@wikimedia/codex-search","19x1l!",[
34]],["mediawiki.template","bca94"],["mediawiki.template.mustache","199kg",[39]],["mediawiki.apipretty","wiuwr"],["mediawiki.api","18kbx",[70,104]],["mediawiki.content.json","f49yo"],["mediawiki.confirmCloseWindow","1ewwa"],["mediawiki.debug","lt46u",[187]],["mediawiki.diff","paqy5"],["mediawiki.diff.styles","yhce5"],["mediawiki.feedback","1eckr",[360,195]],["mediawiki.feedlink","12foc"],["mediawiki.filewarning","6zkbo",[187,199]],["mediawiki.ForeignApi","6vgsr",[52]],["mediawiki.ForeignApi.core","llzm2",[77,42,184]],["mediawiki.helplink","wjdrt"],["mediawiki.hlist","15zvc"],["mediawiki.htmlform","1gl9d",[23,80]],["mediawiki.htmlform.ooui","1m5pb",[187]],["mediawiki.htmlform.styles","ob4wt"],["mediawiki.htmlform.ooui.styles","as95t"],["mediawiki.icon","17xpk"],["mediawiki.inspect","88qa7",[64,80]],["mediawiki.notification","1yf7b",[80,86]],["mediawiki.notification.convertmessagebox","1kd6x",[61]],["mediawiki.notification.convertmessagebox.styles","19vc0"],["mediawiki.String","1vc9s"],[
"mediawiki.pager.styles","eo2ge"],["mediawiki.pager.tablePager","1tupc"],["mediawiki.pulsatingdot","1i1zo"],["mediawiki.searchSuggest","1354y",[27,42]],["mediawiki.storage","2gicm",[80]],["mediawiki.Title","1cw9f",[64,80]],["mediawiki.Upload","ooev2",[42]],["mediawiki.ForeignUpload","2bu58",[51,71]],["mediawiki.Upload.Dialog","198dv",[74]],["mediawiki.Upload.BookletLayout","178we",[71,78,33,190,195,200,201]],["mediawiki.ForeignStructuredUpload.BookletLayout","k1634",[72,74,108,167,161]],["mediawiki.toc","1jhap",[83]],["mediawiki.Uri","7vjqw",[80]],["mediawiki.user","5mz30",[42,83]],["mediawiki.userSuggest","1hhzv",[27,42]],["mediawiki.util","1f3c9",[14,11]],["mediawiki.checkboxtoggle","159pl"],["mediawiki.checkboxtoggle.styles","1b0zv"],["mediawiki.cookie","gf83y",[17]],["mediawiki.experiments","dhcyy"],["mediawiki.editfont.styles","1rala"],["mediawiki.visibleTimeout","xcitq"],["mediawiki.action.edit","mstk4",[30,88,42,85,163]],["mediawiki.action.edit.styles","ra6er"],[
"mediawiki.action.edit.collapsibleFooter","za3yf",[24,59,69]],["mediawiki.action.edit.preview","1kz6y",[25,114,78]],["mediawiki.action.history","psppn",[24]],["mediawiki.action.history.styles","w2lii"],["mediawiki.action.protect","1dt0w",[23,187]],["mediawiki.action.view.metadata","f24h2",[99]],["mediawiki.action.view.postEdit","1o3gf",[104,61,187,206]],["mediawiki.action.view.redirect","iqcjx"],["mediawiki.action.view.redirectPage","1argw"],["mediawiki.action.edit.editWarning","ihdqq",[30,44,104]],["mediawiki.action.view.filepage","9fmxy"],["mediawiki.action.styles","1jp30"],["mediawiki.language","15eby",[102]],["mediawiki.cldr","w8zqb",[103]],["mediawiki.libs.pluralruleparser","1kwne"],["mediawiki.jqueryMsg","mkgu4",[64,101,80,5]],["mediawiki.language.months","1iag2",[101]],["mediawiki.language.names","41ki5",[101]],["mediawiki.language.specialCharacters","fwyi5",[101]],["mediawiki.libs.jpegmeta","1h4oh"],["mediawiki.page.gallery","1n4q2",[110,80]],["mediawiki.page.gallery.styles",
"l984x"],["mediawiki.page.gallery.slideshow","1f4yv",[42,190,209,211]],["mediawiki.page.ready","1uex0",[42]],["mediawiki.page.watch.ajax","45qm7",[42]],["mediawiki.page.preview","138ft",[24,30,42,46,47,187]],["mediawiki.page.image.pagination","iyctm",[25,80]],["mediawiki.rcfilters.filters.base.styles","3sj4r"],["mediawiki.rcfilters.highlightCircles.seenunseen.styles","bi6k7"],["mediawiki.rcfilters.filters.ui","lvrjs",[24,77,78,158,196,203,205,206,207,209,210]],["mediawiki.interface.helpers.styles","1gn7p"],["mediawiki.special","1gey2"],["mediawiki.special.apisandbox","1ces1",[24,77,178,164,186]],["mediawiki.special.block","1sepj",[55,161,177,168,178,175,203]],["mediawiki.misc-authed-ooui","g3hvq",[56,158,163]],["mediawiki.misc-authed-pref","16eja",[5]],["mediawiki.misc-authed-curate","1vp4k",[16,25,42]],["mediawiki.special.changeslist","1gjem"],["mediawiki.special.changeslist.watchlistexpiry","c31m7",[120,206]],["mediawiki.special.changeslist.enhanced","1kflq"],[
"mediawiki.special.changeslist.legend","8vaeq"],["mediawiki.special.changeslist.legend.js","qa88i",[24,83]],["mediawiki.special.contributions","1luqq",[24,104,161,186]],["mediawiki.special.edittags","79img",[13,23]],["mediawiki.special.import.styles.ooui","1hzv9"],["mediawiki.special.changecredentials","f9fqt"],["mediawiki.special.changeemail","10bxu"],["mediawiki.special.preferences.ooui","c3up5",[44,85,62,69,168,163,195]],["mediawiki.special.preferences.styles.ooui","1ap1t"],["mediawiki.special.revisionDelete","cvqd5",[163]],["mediawiki.special.search","11pp3",[180]],["mediawiki.special.search.commonsInterwikiWidget","1f9ou",[77,42]],["mediawiki.special.search.interwikiwidget.styles","11r2m"],["mediawiki.special.search.styles","wb7pt"],["mediawiki.special.unwatchedPages","mk9s7",[42]],["mediawiki.special.upload","8kptc",[25,42,44,108,120,39]],["mediawiki.special.userlogin.common.styles","7spfn"],["mediawiki.special.userlogin.login.styles","ztgtl"],["mediawiki.special.createaccount",
"mbk5h",[42]],["mediawiki.special.userlogin.signup.styles","en8j6"],["mediawiki.special.userrights","4k0n6",[23,62]],["mediawiki.special.watchlist","lr1n3",[42,187,206]],["mediawiki.ui","zgc19"],["mediawiki.ui.checkbox","umtyv"],["mediawiki.ui.radio","zwcw3"],["mediawiki.ui.anchor","1yxgk"],["mediawiki.ui.button","w4v3u"],["mediawiki.ui.input","efg62"],["mediawiki.ui.icon","gr0en"],["mediawiki.widgets","ft689",[42,159,190,200,201]],["mediawiki.widgets.styles","1x5du"],["mediawiki.widgets.AbandonEditDialog","1tcrg",[195]],["mediawiki.widgets.DateInputWidget","espqr",[162,33,190,211]],["mediawiki.widgets.DateInputWidget.styles","1rb1t"],["mediawiki.widgets.visibleLengthLimit","m325n",[23,187]],["mediawiki.widgets.datetime","9s7gz",[80,187,206,210,211]],["mediawiki.widgets.expiry","m5uji",[164,33,190]],["mediawiki.widgets.CheckMatrixWidget","k9si1",[187]],["mediawiki.widgets.CategoryMultiselectWidget","x4tey",[51,190]],["mediawiki.widgets.SelectWithInputWidget","yzuek",[169,190]],[
"mediawiki.widgets.SelectWithInputWidget.styles","vkr7h"],["mediawiki.widgets.SizeFilterWidget","1hmr4",[171,190]],["mediawiki.widgets.SizeFilterWidget.styles","ceybj"],["mediawiki.widgets.MediaSearch","13spi",[51,78,190]],["mediawiki.widgets.Table","p2qhh",[190]],["mediawiki.widgets.TagMultiselectWidget","1erse",[190]],["mediawiki.widgets.UserInputWidget","jsk5k",[42,190]],["mediawiki.widgets.UsersMultiselectWidget","1m6vb",[42,190]],["mediawiki.widgets.NamespacesMultiselectWidget","pwj2l",[190]],["mediawiki.widgets.TitlesMultiselectWidget","gt95w",[158]],["mediawiki.widgets.TagMultiselectWidget.styles","1rjw4"],["mediawiki.widgets.SearchInputWidget","z70j2",[68,158,206]],["mediawiki.widgets.SearchInputWidget.styles","9327p"],["mediawiki.watchstar.widgets","a5i1b",[186]],["mediawiki.deflate","1ci7b"],["oojs","ewqeo"],["mediawiki.router","1fux1",[184]],["oojs-ui","1jh3r",[193,190,195]],["oojs-ui-core","6ht0w",[101,184,189,188,197]],["oojs-ui-core.styles","15ugt"],["oojs-ui-core.icons",
"1aatn"],["oojs-ui-widgets","1qtbb",[187,192]],["oojs-ui-widgets.styles","67cm0"],["oojs-ui-widgets.icons","13oej"],["oojs-ui-toolbars","1ajk8",[187,194]],["oojs-ui-toolbars.icons","1rckx"],["oojs-ui-windows","go7v9",[187,196]],["oojs-ui-windows.icons","82fqv"],["oojs-ui.styles.indicators","pkppt"],["oojs-ui.styles.icons-accessibility","19io4"],["oojs-ui.styles.icons-alerts","1ecth"],["oojs-ui.styles.icons-content","1axez"],["oojs-ui.styles.icons-editing-advanced","pu86m"],["oojs-ui.styles.icons-editing-citation","s83ic"],["oojs-ui.styles.icons-editing-core","b0nii"],["oojs-ui.styles.icons-editing-list","k16lg"],["oojs-ui.styles.icons-editing-styling","1icse"],["oojs-ui.styles.icons-interactions","9rmip"],["oojs-ui.styles.icons-layout","j8i09"],["oojs-ui.styles.icons-location","1w312"],["oojs-ui.styles.icons-media","mwvrc"],["oojs-ui.styles.icons-moderation","x4bao"],["oojs-ui.styles.icons-movement","1h2cu"],["oojs-ui.styles.icons-user","136n7"],["oojs-ui.styles.icons-wikimedia",
"uqfg5"],["skins.vector.user","1b93e",[],0],["skins.vector.user.styles","1rlz1",[],0],["skins.vector.search","wv61p!",[38,77]],["skins.vector.styles.legacy","gd15c"],["skins.vector.styles","zyrlx"],["skins.vector.icons.js","a3qmg"],["skins.vector.icons","1bfrk"],["skins.vector.es6","gduyd!",[84,112,113,69,78,219]],["skins.vector.js","xz33n",[112,219]],["skins.vector.legacy.js","omaiv",[112]],["ext.categoryTree","1e8m5",[42]],["ext.categoryTree.styles","1d80w"],["ext.cite.styles","1y312"],["ext.cite.style","gn7m1"],["ext.cite.visualEditor.core","s005r",[312]],["ext.cite.visualEditor","1j6d9",[227,226,228,199,202,206]],["ext.cite.ux-enhancements","14f0k"],["ext.citeThisPage","zt3yx"],["ext.codeEditor","1ma6m",[233],3],["jquery.codeEditor","1blhe",[235,234,254,195],3],["ext.codeEditor.icons","3w6kb"],["ext.codeEditor.ace","1brj0",[],4],["ext.codeEditor.ace.modes","1o3y6",[235],4],["ext.inputBox.styles","1dv4m"],["ext.interwiki.specialpage","lsm82"],["mmv","1e2nz",[15,19,31,77,244]],[
"mmv.ui.ondemandshareddependencies","1ca30",[239,186]],["mmv.ui.download.pane","14gi0",[151,158,240]],["mmv.ui.reuse.shareembed","1hm4h",[158,240]],["mmv.ui.tipsyDialog","1vews",[239]],["mmv.bootstrap","ew7dc",[185,155,157,246]],["mmv.bootstrap.autostart","dgnjl",[244]],["mmv.head","1vrgu",[69,78]],["ext.nuke.confirm","14ono",[104]],["pdfhandler.messages","9fsnr"],["ext.ReplaceText","1ola7"],["ext.ReplaceTextStyles","1doqs"],["ext.pygments","3yewq"],["ext.pygments.linenumbers","1ra7j",[80]],["ext.geshi.visualEditor","5oikr",[304]],["ext.wikiEditor","1iywu",[30,32,107,78,158,202,203,204,205,209,39],3],["ext.wikiEditor.styles","rlj9c",[],3],["ext.wikiEditor.images","1mt9y"],["ext.wikiEditor.realtimepreview","1uf6k",[254,256,114,67,69,206]],["ext.templateData","mmxgk"],["ext.templateDataGenerator.editPage","1e7eh"],["ext.templateDataGenerator.data","hmz7t",[184]],["ext.templateDataGenerator.editTemplatePage.loading","60i01"],["ext.templateDataGenerator.editTemplatePage","1gba0",[258,263,
260,30,358,78,190,195,206,207,210]],["ext.templateData.images","dm1md"],["ext.youtube.lazyload","16y1w"],["ext.uploadWizard.page","nlv9f",[268],5],["ext.uploadWizard.page.styles","rcuz8"],["ext.uploadWizard.uploadCampaign.display","176xb"],["ext.uploadWizard","16kok",[24,25,44,85,48,59,108,78,158,167,161,199,203,206,208,210],5],["socket.io","1g15q"],["dompurify","1p3gn"],["color-picker","jq79v"],["unicodejs","1r04c"],["papaparse","oiasq"],["rangefix","1ext9"],["spark-md5","9kzx3"],["ext.visualEditor.supportCheck","13rwp",[],6],["ext.visualEditor.sanitize","kpn5b",[270,293],6],["ext.visualEditor.progressBarWidget","1rnzo",[],6],["ext.visualEditor.tempWikitextEditorWidget","k7mf7",[85,78],6],["ext.visualEditor.desktopArticleTarget.init","1vu6g",[278,276,279,290,30,77,112,69],6],["ext.visualEditor.desktopArticleTarget.noscript","1nhq2"],["ext.visualEditor.targetLoader","11gzf",[292,290,30,69,78],6],["ext.visualEditor.desktopTarget","1eg0k",[],6],["ext.visualEditor.desktopArticleTarget",
"ww1zl",[296,301,283,306],6],["ext.visualEditor.collabTarget","9w0jk",[294,300,85,158,206,207],6],["ext.visualEditor.collabTarget.desktop","1s5xf",[285,301,283,306],6],["ext.visualEditor.collabTarget.init","6xvpa",[276,158,186],6],["ext.visualEditor.collabTarget.init.styles","8xxz4"],["ext.visualEditor.ve","1l3o4",[],6],["ext.visualEditor.track","1ma8w",[289],6],["ext.visualEditor.core.utils","1jeye",[290,186],6],["ext.visualEditor.core.utils.parsing","yk6md",[289],6],["ext.visualEditor.base","jtrk8",[291,292,272],6],["ext.visualEditor.mediawiki","1v3w5",[293,282,28,358],6],["ext.visualEditor.mwsave","1p8b3",[304,23,25,46,47,206],6],["ext.visualEditor.articleTarget","14ceq",[305,295,160],6],["ext.visualEditor.data","rep0t",[294]],["ext.visualEditor.core","ekdnv",[277,276,14,273,274,275],6],["ext.visualEditor.commentAnnotation","oduq3",[298],6],["ext.visualEditor.rebase","1faw8",[271,315,299,212,269],6],["ext.visualEditor.core.desktop","1ncrc",[298],6],["ext.visualEditor.welcome",
"2nhv1",[186],6],["ext.visualEditor.switching","tfa3p",[42,186,198,201,203],6],["ext.visualEditor.mwcore","nqp80",[316,294,303,302,119,67,8,158],6],["ext.visualEditor.mwextensions","1jh3r",[297,326,320,322,307,324,309,321,310,312],6],["ext.visualEditor.mwextensions.desktop","1jh3r",[305,311,75],6],["ext.visualEditor.mwformatting","12g9x",[304],6],["ext.visualEditor.mwimage.core","4tjj1",[304],6],["ext.visualEditor.mwimage","1otob",[327,308,172,33,209],6],["ext.visualEditor.mwlink","1uhxk",[304],6],["ext.visualEditor.mwmeta","rlok1",[310,97],6],["ext.visualEditor.mwtransclusion","si1rt",[304,175],6],["treeDiffer","1i331"],["diffMatchPatch","1rln1"],["ext.visualEditor.checkList","a9z1l",[298],6],["ext.visualEditor.diffing","1r4y3",[314,298,313],6],["ext.visualEditor.diffPage.init.styles","tksjv"],["ext.visualEditor.diffLoader","1rup1",[282],6],["ext.visualEditor.diffPage.init","19kxj",[318,186,198,201],6],["ext.visualEditor.language","8lcn2",[298,358,106],6],[
"ext.visualEditor.mwlanguage","mm4ip",[298],6],["ext.visualEditor.mwalienextension","12szj",[304],6],["ext.visualEditor.mwwikitext","125nq",[310,85],6],["ext.visualEditor.mwgallery","sg2pn",[304,110,172,209],6],["ext.visualEditor.mwsignature","o0x91",[312],6],["ext.visualEditor.icons","1jh3r",[328,329,199,200,201,203,204,205,206,207,210,211,212,197],6],["ext.visualEditor.icons-licenses","lexd3"],["ext.visualEditor.moduleIcons","1b9ek"],["ext.visualEditor.moduleIndicators","mthpt"],["ext.drawioeditor.styles","phedi"],["ext.drawioeditor","upkxe",[32,42]],["ext.drawioconnector.visualEditor","8495t",[304]],["ext.confirmEdit.editPreview.ipwhitelist.styles","11y4q"],["ext.confirmEdit.visualEditor","rlq1b",[359]],["ext.confirmEdit.simpleCaptcha","14a9d"],["ext.scribunto.errors","m6dtq",[190]],["ext.scribunto.logs","c053i"],["ext.scribunto.edit","1lfgm",[25,42]],["mobile.pagelist.styles","8ehyf"],["mobile.pagesummary.styles","1v7bu"],["mobile.placeholder.images","1kddq"],[
"mobile.userpage.styles","13q5r"],["mobile.startup.images","hkpza"],["mobile.init.styles","1r4ds"],["mobile.init","1cqj1",[77,348]],["mobile.ooui.icons","1f61y"],["mobile.user.icons","13fb6"],["mobile.startup","1wlp6",[113,185,69,40,155,157,78,346,339,340,341,343]],["mobile.editor.overlay","1njw0",[44,85,61,156,160,350,348,347,186,203]],["mobile.editor.images","kg9ej"],["mobile.talk.overlays","174sm",[154,349]],["mobile.mediaViewer","1c7mf",[348]],["mobile.languages.structured","1qp3p",[348]],["mobile.special.mobileoptions.styles","15oua"],["mobile.special.mobileoptions.scripts","1aqhn",[348]],["mobile.special.userlogin.scripts","xiewn"],["mobile.special.mobilediff.images","1xbxt"],["jquery.uls.data","149oa"],["ext.confirmEdit.CaptchaInputWidget","ffqyg",[187]],["mediawiki.messagePoster","13b1w",[51]]]);mw.config.set(window.RLCONF||{});mw.loader.state(window.RLSTATE||{});mw.loader.load(window.RLPAGEMODULES||[]);queue=window.RLQ||[];RLQ=[];RLQ.push=function(fn){if(typeof fn==='function'
){fn();}else{RLQ[RLQ.length]=fn;}};while(queue[0]){RLQ.push(queue.shift());}NORLQ={push:function(){}};}());}
