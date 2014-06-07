/**
 * Created by Alejandro Suarez on 07/06/14.
 */
/**
 * Constructor model
 * @constructor
 */
 function Model() {
    this.class_name='';
    this.setAttribute = function (nameAttr, value,db){
        this.data[nameAttr] = value;
    };
    this.getAttribute = function (nameAttr){
        return this.data[nameAttr];
    };
 }