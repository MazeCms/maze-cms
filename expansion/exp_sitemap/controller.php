<?php



class Sitemap_Controller extends Controller {
    
    public function actionDisplay($sitemap_id) {
        
        $model = $this->model('SitemapModel');
        $model->find(['m.sitemap_id'=>$sitemap_id, 'm.enable_html'=>1]);

        if(!$model->map){
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) ни чего не найдено", ['id'=>$sitemap_id]));
        }
        
        $model->saveSitemapVisits('html');
        $links = $model->getSortParentLink();
        
        return $this->renderPart('default', null, null, ['model'=>$model, 'links'=>$links]); 
    }
    
    public function actionXml($sitemap_id) {
        $model = $this->model('SitemapModel');
        $model->find(['m.sitemap_id'=>$sitemap_id, 'm.enable_xml'=>1]);

        if(!$model->map){
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) ни чего не найдено", ['id'=>$sitemap_id]));
        }
        
        $model->saveSitemapVisits('xml');
        if(!$model->getLink()){
            throw new maze\exception\NotFoundHttpException(Text::_("По текущему ID({id}) не найдено  не одной ссылки XML карты сайта ({name})", ['id'=>$sitemap_id, 'name'=>$model->map->name]));
        }
        
        $contents = $model->getXMLContents();
        $components = RC::app()->getComponent('sitemap');
 
        if($components->config->getVar('download')){
            RC::app()->response->sendContentAsFile($contents, $model->getRoute()->alias.'.xml', ['mimeType'=>'application/xml']);
        }else{
            RC::app()->response->data =$contents;
            RC::app()->response->format = \maze\document\Response::FORMAT_RAW;
            RC::app()->response->getDocument()->setHeader('Content-Type', 'text/xml');
        }
        
    }
    
    
}

?>