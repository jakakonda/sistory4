<?php

namespace App\Console\Commands;

use App\Helpers\ElasticHelpers;
use App\Helpers\EntitySelect;
use App\Helpers\FileHelpers;
use App\Helpers\Si4Util;
use App\Models\Elastic\EntityElastic;
use App\Models\Entity;
use App\Xsd\AnyTypeHandler;
use App\Xsd\AsTextTypeHandler;
use App\Xsd\Base64TypeHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\BaseTypesHandler;
use GoetasWebservices\Xsd\XsdToPhpRuntime\Jms\Handler\XmlSchemaDateHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use JMS\Serializer\Handler\HandlerRegistryInterface;
use JMS\Serializer\SerializerBuilder;

class ThumbsCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thumbs:create {entityId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create thumbs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $entityId = $this->argument('entityId');
        $this->comment("Fetching entity {$entityId}");

        $elasticEntities = ElasticHelpers::searchByIdArray([$entityId]);
        $elasticEntity = Si4Util::pathArg($elasticEntities, $entityId."/_source/data");
        $handle_id = Si4Util::pathArg($elasticEntity, "id");
        $firstFile = Si4Util::pathArg($elasticEntity, "files/0");
        $firstFileHandleId = Si4Util::pathArg($firstFile, "id");
        $firstFileName = Si4Util::pathArg($firstFile, "ownerId");

        if ($firstFileHandleId && $firstFileName) {
            $this->info("- Recreating thumbnail for entity {$entityId} handle_id={$handle_id}, file_handle={$firstFileHandleId} ({$firstFileName})");

            $storageName = FileHelpers::getPublicStorageName($firstFileHandleId, $firstFileName);
            $fullPath = storage_path('app')."/".$storageName;

            if (file_exists($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);

                $imInput = $fullPath;
                if (strtolower($ext) == "pdf") $imInput .= "[0]";

                $image = new \Imagick($imInput);
                $image->setResolution(320, 0);
                $image->setCompressionQuality(85);
                $image->setImageFormat('jpeg');
                $image->writeImage($fullPath.SI4_THUMB_FILE_POSTFIX);
                $image->clear();
                $image->destroy();
            }
        }
    }
}
