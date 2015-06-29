<?php
/**.
 * User: Andrey Shamis
 * Date: 5/25/15
 * Time: 12:29 PM
 */
require_once( realpath( dirname( __FILE__ ) ).'/../HTML_HEADER.php');
?>
<body>
    <div id="canvas"> </div>
		<script>
			
			var manualControl   = false;
			var longitude       = 0;
			var latitude        = 0;
			var savedX;
			var savedY;
			var savedLongitude;
			var savedLatitude;
			
			// panoramas background
			var panoramasArray  = ["01.jpg","02.jpg","03.jpg","04.jpg","05.jpg","06.jpg","12.jpg","13.jpg","14.jpg"];
			var panoramaNumber  = Math.floor(Math.random()*panoramasArray.length);


            container           = document.getElementById( 'canvas' );
            document.body.appendChild( container );
			// setting up the renderer
			renderer            = new THREE.WebGLRenderer();
			renderer.setSize(window.innerWidth, window.innerHeight);
			document.body.appendChild(renderer.domElement);
			
			// creating a new scene
			var scene           = new THREE.Scene();
			
			// adding a camera
			var camera          = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 1, 1000);
			camera.target       = new THREE.Vector3(0, 0, 0);

			// creation of a big sphere geometry
			var sphere          = new THREE.SphereGeometry(100, 100, 40);
			sphere.applyMatrix(new THREE.Matrix4().makeScale(-1, 1, 1));

			// creation of the sphere material
			var sphereMaterial  = new THREE.MeshBasicMaterial();
			sphereMaterial.map  = THREE.ImageUtils.loadTexture(getImagePath(panoramasArray[panoramaNumber]))

			// geometry + material = mesh (actual object)
			var sphereMesh      = new THREE.Mesh(sphere, sphereMaterial);
			scene.add(sphereMesh);

			// listeners
			document.addEventListener("mousedown", onDocumentMouseDown, false);
			document.addEventListener("mousemove", onDocumentMouseMove, false);
			document.addEventListener("mouseup", onDocumentMouseUp, false);
            document.addEventListener("ontouchmove",  onDocumentOnTouchMove, false);
            document.addEventListener("ontouchend",  onDocumentTouchend, false);
            document.addEventListener("touchstart",  onDocumenttouchstart, false);

            render();
               
            function render(){
				requestAnimationFrame(render);
				if(!manualControl){
					longitude += 0.1;
				}
				// limiting latitude from -85 to 85 (cannot point to the sky or under your feet)
                latitude        = Math.max(-85, Math.min(85, latitude));
				// moving the camera according to current latitude (vertical movement) and longitude (horizontal movement)
				camera.target.x = 500 * Math.sin(THREE.Math.degToRad(90 - latitude)) * Math.cos(THREE.Math.degToRad(longitude));
				camera.target.y = 500 * Math.cos(THREE.Math.degToRad(90 - latitude));
				camera.target.z = 500 * Math.sin(THREE.Math.degToRad(90 - latitude)) * Math.sin(THREE.Math.degToRad(longitude));
				camera.lookAt(camera.target);
				// calling again render function
				renderer.render(scene, camera);
				
			}

            function onDocumenttouchstart(event){
                event.preventDefault();
                manualControl   = true;
                savedX          = event.clientX;
                savedY          = event.clientY;
                savedLongitude  = longitude;
                savedLatitude   = latitude;

            }
			// when the mouse is pressed, we switch to manual control and save current coordinates
			function onDocumentMouseDown(event){
				event.preventDefault();
				manualControl   = true;
				savedX          = event.clientX;
				savedY          = event.clientY;
				savedLongitude  = longitude;
				savedLatitude   = latitude;
                //console.log("onDocumentMouseDown");
			}

            function onDocumentOnTouchMove(event) {
                //event.preventDefault();
                if(manualControl){
                    longitude   = (savedX - event.clientX) * 0.1 + savedLongitude;
                    latitude    = (event.clientY - savedY) * 0.1 + savedLatitude;
                }
                //event.preventDefault();
            }

            // when the mouse moves, if in manual contro we adjust coordinates
            function onDocumentMouseMove(event){

                if(manualControl){
                    longitude   = (savedX - event.clientX) * 0.1 + savedLongitude;
                    latitude    = (event.clientY - savedY) * 0.1 + savedLatitude;
                    camera.updateProjectionMatrix();
                }
                //console.log("onDocumentMouseMove");
            }

            if (document.addEventListener) {
                if ('onwheel' in document) {
                    // IE9+, FF17+, Ch31+
                    document.addEventListener("wheel", onWheel);
                } else if ('onmousewheel' in document) {
                    // устаревший вариант события
                    document.addEventListener("mousewheel", onWheel);
                } else {
                    // Firefox < 17
                    document.addEventListener("MozMousePixelScroll", onWheel);
                }
            } else { // IE8-
                document.attachEvent("onmousewheel", onWheel);
            }

            function onWheel(e) {
                e = e || window.event;
                //console.log(e);
                // wheelDelta не дает возможность узнать количество пикселей
                var delta       = e.deltaY ||  e.wheelDelta; //e.detail ||
//                var  delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
                var zoomFactor  = (1+1/delta);
                Log(zoomFactor);

                if(!manualControl){
                    camera.fov *= zoomFactor;
                    if(camera.fov < 10){
                        camera.fov  = 10;
                    }
                    if(camera.fov > 89){
                        camera.fov  = 89;
                    }
                    camera.updateProjectionMatrix();
                    Log(camera.fov);
                    Log("zoomFactor:"+ zoomFactor);
                }
                // wheelDelta не дает возможность узнать количество пикселей
                //var delta = e.deltaY || e.detail || e.wheelDelta;
                //var info = document.getElementByName('canvas');
                //info.innerHTML = +info.innerHTML + delta;
                e.preventDefault ? e.preventDefault() : (e.returnValue = false);
            }
			// when the mouse moves, if in manual contro we adjust coordinates


			// when the mouse is released, we turn manual control off
			function onDocumentMouseUp(event){
				manualControl = false;
                //console.log("onDocumentMouseUp");
			}

            function onDocumentTouchend(event){
                manualControl = false;
            }

			// pressing a key (actually releasing it) changes the texture map
			document.onkeyup = function(event){
				panoramaNumber = (panoramaNumber + 1) % panoramasArray.length;
				sphereMaterial.map = THREE.ImageUtils.loadTexture(getImagePath(panoramasArray[panoramaNumber]));
            }

            document.touchend = function(event){
                panoramaNumber = (panoramaNumber + 1) % panoramasArray.length;
                sphereMaterial.map = THREE.ImageUtils.loadTexture(getImagePath(panoramasArray[panoramaNumber]));
            }

            function TestJQ(){
                console.log('Test JQ');
                $("#debug").html("Test JQ");
            }

            function ShowContainer(){
                $('#light').css("display","block");
                $('#fade').css("display","block");
            }
		</script>

    <div id="debug" style="width: 300px;border: 1px solid red;" onclick='TestJQ()'>sdadasd</div>

        <p>This is the main content. To display a lightbox click
            <a href = "javascript:void(0)" onclick = "ShowContainer()">here</a>
        </p>
        <div id="light" class="white_content">This is the lightbox content.
            <a href = "javascript:void(0)" onclick = "document.getElementById('light').style.display='none';document.getElementById('fade').style.display='none'">Close</a>
        </div>
        <div id="fade" class="black_overlay"></div>

    </body>
</html>