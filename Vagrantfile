ENV['VAGRANT_DEFAULT_PROVIDER'] = 'docker'

vagrant_root = File.dirname(__FILE__)
require 'yaml'
if !File.exist?(vagrant_root + '/vagrant-settings.yml')
  raise 'Configuration file not found! Please copy vagrant-settings.yml.dist to vagrant-settings.yml and try again.'
  exit
end
$settings = YAML.load_file(vagrant_root + '/vagrant-settings.yml')

Vagrant.configure(2) do |config|
    config.vm.define "nasta-submissions-db" do |db|
        db.vm.synced_folder "docker/mysql/data", "/var/lib/mysql",
          owner: "mysql", group: "mysql"

        db.vm.provider "docker" do |d|
            # Run test environment on port 33306
            d.ports = $settings['ports']['mysql']

            d.name = "nasta-submissions-db"
            d.build_dir = "docker/mysql/"

            d.env = {
                "MYSQL_ROOT_PASSWORD" => $settings['mysql']['password'],
            }
        end
    end

    config.vm.define "nasta-submissions-pma" do |app|
        app.vm.provider "docker" do |d|
            d.name = "nasta-submissions-pma"
            d.image = "phpmyadmin/phpmyadmin"

            d.ports = $settings['ports']['pma']

            d.link("nasta-submissions-db:db")

            d.env = {
                "PMA_USER" => "root",
                "PMA_PASSWORD" => $settings['mysql']['password']
            }
        end
    end

    config.vm.define "nasta-submissions-mailhog" do |app|
        app.vm.provider "docker" do |d|
            d.name = "nasta-submissions-mailhog"
            d.image = "mailhog/mailhog"

            d.ports = $settings['ports']['mailhog']
        end
    end


    config.vm.define "nasta-submissions-base" do |app|
        app.vm.provider "docker" do |d|
            d.dockerfile = "docker/Dockerfile"
            d.remains_running = false
            d.build_dir = "."
            d.name = "nasta-submissions-base"
            d.build_args = ["--tag=nasta-sub:base"]
        end
    end

    config.vm.define "nasta-submissions-http" do |app|
        app.vm.provider "docker" do |d|
            d.ports = $settings['ports']['http']

            d.build_dir = "docker/http"
            d.name = "nasta-submissions-http"
            d.build_args = ["--tag=nasta-sub:http"]   

            d.link("nasta-submissions-db:db")
            d.link("nasta-submissions-mailhog:mailhog")

            d.volumes = ["#{vagrant_root}:/src"]
            config.vm.synced_folder '.', '/vagrant', :disabled => true

            d.env = {
                "VM_ENV" => "dev",
                "UID" => $settings['userid']
            }
        end
    end

    config.vm.define "nasta-submissions-queue" do |app|
        app.vm.provider "docker" do |d|
            d.build_dir = "docker/queue"
            d.name = "nasta-submissions-queue"
            d.build_args = ["--tag=nasta-sub:queue"]  
            d.cmd = ["/start.sh", "--timeout=10", "--tries=3", "--delay=10"]

            d.link("nasta-submissions-db:db")
            d.link("nasta-submissions-mailhog:mailhog")

            d.volumes = ["#{vagrant_root}:/src"]
            config.vm.synced_folder '.', '/vagrant', :disabled => true


            d.env = {
                "VM_ENV" => "dev",
                "UID" => $settings['userid']
            }
        end
    end

    config.vm.define "nasta-submissions-queue-downloads" do |app|
        app.vm.provider "docker" do |d|
            d.build_dir = "docker/queue"
            d.name = "nasta-submissions-queue-downloads"
            d.build_args = ["--tag=nasta-sub:queue"]   
            d.cmd = ["/start.sh", "--queue=process,downloads,uploads", "--timeout=300", "--memory=256", "--delay=120"]

            d.link("nasta-submissions-db:db")
            d.link("nasta-submissions-mailhog:mailhog")

            d.volumes = ["#{vagrant_root}:/src"]
            config.vm.synced_folder '.', '/vagrant', :disabled => true

            d.env = {
                "VM_ENV" => "dev",
                "UID" => $settings['userid']
            }
        end
    end

    config.vm.define "nasta-submissions-cron" do |app|
        app.vm.provider "docker" do |d|
            d.build_dir = "docker/cron"
            d.name = "nasta-submissions-cron"
            d.build_args = ["--tag=nasta-sub:cron"]   

            d.link("nasta-submissions-db:db")
            d.link("nasta-submissions-mailhog:mailhog")

            d.volumes = ["#{vagrant_root}:/src"]
            config.vm.synced_folder '.', '/vagrant', :disabled => true

            d.env = {
                "VM_ENV" => "dev",
                "UID" => $settings['userid']
            }
        end
    end
end