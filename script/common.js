/**
 * Created by werd on 6/25/15.
 */


function Log(msg){

    try{
        console.log(msg);
    }
    catch (ex){

    }
}

function getImagePath(image){
    var path = "/image/" + image;
    Log("Loading:" + path);
    return path;
}