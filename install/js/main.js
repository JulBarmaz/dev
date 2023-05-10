//  BARMAZ erp system
//  Copyright (c) BARMAZ Group
//  Web: https://BARMAZ.ru/
//  Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
//  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//  Revision: 135 (2023-05-10 14:11:23)
// 


$(document).ready(function() {
	$('p.sql_error').bind('click',function(el){ $(this).next().toggle(); });
	$("#"+tb).parent().addClass("active");
	$('input[type=submit]').removeAttr("disabled");
	$('input[type=submit]').bind('click',function(el){ 
		$(this).attr("disabled", "true");
		$('#inst_frm').submit();
		return true; 
	});
});
function toggleDemoData(elm)
{
	var imod=document.getElementsByName("mod_inst[]");
	//console.log(imod);
	var imod_dem=document.getElementsByName("mod_inst_demo[]");
	//console.log(imod_dem);
	var dmap= new Map();
	for (let i = 0; i < imod_dem.length; i++) {
		dmap.set(imod_dem[i].id,i);
	};
	//console.log("dmap",dmap);	

	imod.forEach(function(value) {
		//console.log(value.checked,value.type);
		if(value.type=='checkbox'){
			value.checked=elm.checked;
			let idd=value.id.replace('i_','id_');
			//console.log(idd,value.id);
			//let ind_d=dmap.get(idd);
			//console.log(imod_dem[ind_d]);
			if(dmap.has(idd)){
			imod_dem[dmap.get(idd)].checked=elm.checked;;
			}
			/*
			value.addEventListener('change', () => {
				console.log(value.checked,value.id);
				if(!value.checked){
					if(dmap.has(idd)){
						imod_dem[dmap.get(idd)].checked=false;
						imod_dem[dmap.get(idd)].addEventListener('change', () => {
							console.log("���������� ��������")
						});			
					}
				}	
		});
		*/
		
		}
			
		});	
}
function toggleDemoDataMod(value)
{
	//console.log(value);
	var imod_dem=document.getElementsByName("mod_inst_demo[]");
	var dmap= new Map();
	for (let i = 0; i < imod_dem.length; i++) {
		dmap.set(imod_dem[i].id,i);	
	};
	if(value.type=='checkbox'){
		//value.checked=elm.checked;
		let idd=value.id.replace('i_','id_');
		//console.log(idd,value.id);
		//let ind_d=dmap.get(idd);
		//console.log(imod_dem[ind_d]);
		if(dmap.has(idd)){
		imod_dem[dmap.get(idd)].checked=value.checked;
		}
	};	
}	
